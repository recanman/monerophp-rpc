<?php

declare(strict_types=1);

namespace RefRing\MoneroRpcPhp\Tests\integration;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Depends;
use RefRing\MoneroRpcPhp\ClientBuilder;
use RefRing\MoneroRpcPhp\DaemonRpcClient;
use RefRing\MoneroRpcPhp\Exception\InvalidAddressException;
use RefRing\MoneroRpcPhp\Exception\InvalidDestinationException;
use RefRing\MoneroRpcPhp\Model\Recipient;
use RefRing\MoneroRpcPhp\Model\TransferType;
use RefRing\MoneroRpcPhp\Tests\TestHelper;
//use RefRing\MoneroRpcPhp\Tests\Util\StdOutLogger;
use RefRing\MoneroRpcPhp\WalletRpc\TransferResponse;
use RefRing\MoneroRpcPhp\WalletRpcClient;

final class TransferTest extends TestCase
{
    public static array $seeds = [TestHelper::WALLET_1_MNEMONIC];

    public static array $wallets = [];

    private static DaemonRpcClient $daemonRpcClient;
    private static WalletRpcClient $walletRpcClient;

    public static int $runningBalance = 0;

    private const AMOUNT = 1000000000000;

    public static function tearDownAfterClass(): void
    {
        // Reset the blockchain
        $height = self::$daemonRpcClient->getHeight();
        self::$daemonRpcClient->popBlocks($height->height - 1);
        self::$daemonRpcClient->flushTxpool();
    }

    public static function setUpBeforeClass(): void
    {
        self::$daemonRpcClient = (new ClientBuilder(TestHelper::DAEMON_RPC_URL))
//            ->withLogger(new StdOutLogger())
            ->buildDaemonClient();
        self::$walletRpcClient = (new ClientBuilder(TestHelper::WALLET_RPC_URL))
//            ->withLogger(new StdOutLogger())
            ->buildWalletClient();
        foreach (self::$seeds as $seed) {
            self::$wallets[] = self::$walletRpcClient->restoreDeterministicWallet('', '', $seed);
        }

        self::$daemonRpcClient->generateBlocks(100, TestHelper::MAINNET_ADDRESS_1);
    }

    public function testWallet(): void
    {
        $this->assertSame(self::$seeds[0], self::$wallets[0]->seed);
        self::$walletRpcClient->refresh();
        $this->assertSame(101, self::$walletRpcClient->getHeight()->height);

        $result = self::$walletRpcClient->getBalance(0);
        $this->assertSame(59, $result->blocksToUnlock);
        self::$runningBalance = $result->balance;
    }

    public function testTransferEmptyDestination(): void
    {
        $this->expectException(InvalidDestinationException::class);
        self::$walletRpcClient->transfer([]);
    }

    // Disabled for now because this will give timeouts sometimes because of DNS lookups
    //    public function testTransferInvalidDestination(): void
    //    {
    //        $this->expectException(InvalidAddressException::class);
    //        self::$walletRpcClient->transfer(new Recipient(TestHelper::TESTNET_ADDRESS_1, 100));
    //    }

    public function testTransfer(): TransferResponse
    {
        self::$walletRpcClient->refresh();

        echo 'Current walelt height: '.self::$walletRpcClient->getHeight()->height;

        $result = self::$walletRpcClient->transfer(new Recipient(TestHelper::MAINNET_ADDRESS_1, self::AMOUNT), getTxKey: false, getTxHex: true);

        $this->assertSame(64, strlen($result->txHash));
        $this->assertSame(0, strlen($result->txKey));
        $this->assertGreaterThan(0, $result->amount);
        $this->assertGreaterThan(0, $result->fee);
        self::$runningBalance -= $result->fee;

        $this->assertGreaterThan(0, strlen($result->txBlob));
        $this->assertSame(0, strlen($result->txMetadata));
        $this->assertSame(0, strlen($result->multisigTxset));
        $this->assertSame(0, strlen($result->unsignedTxset));

        $balanceResult = self::$walletRpcClient->getBalance();
        $this->assertSame($balanceResult->balance, self::$runningBalance);

        return $result;
    }

    #[Depends('testTransfer')]
    public function testFee(TransferResponse $transferResponse): void
    {
        $fee = $transferResponse->fee;
        $txWeight = $transferResponse->weight;

        $result = self::$daemonRpcClient->getFeeEstimate(10);
        $this->assertGreaterThan(0, $result->fee);
        $this->assertGreaterThan(0, $result->quantizationMask);

        $expectedFee = ($result->fee * 1 * $txWeight + $result->quantizationMask - 1);
        $this->assertLessThan(0.01, abs(1 - $fee / $expectedFee));
    }

    #[Depends('testTransfer')]
    public function testGetTransfers(TransferResponse $transferResponse): void
    {
        self::$walletRpcClient->refresh();

        $height = self::$daemonRpcClient->getInfo()->height;
        $result = self::$walletRpcClient->getTransfers(true, true, true, true, true);

        $this->assertSame($height - 1, count($result->in));
        //        $this->assertObjectNotHasProperty('out', $result); // Not mined yet
        $this->assertEquals(1, count($result->pending));
        foreach ($result->in as $transaction) {
            $this->assertEquals(TransferType::BLOCK, $transaction->type);
        }
    }

    #[Depends('testTransfer')]
    public function testTransferByTxId(TransferResponse $transferResponse): void
    {

        self::$daemonRpcClient->generateBlocks(1, TestHelper::MAINNET_ADDRESS_1);
        sleep(2);
        $result = self::$walletRpcClient->refresh();
        $this->assertSame(true, $result->receivedMoney);
        $height = self::$walletRpcClient->getHeight()->height;
        $result = self::$walletRpcClient->getTransferByTxid($transferResponse->txHash);

        //        print_r(self::$daemonRpcClient->getTx)

        $this->assertSame(1, count($result->transfers));
        $this->assertEquals($result->transfer, $result->transfers[0]);

        $transfer = $result->transfer;
        print_r($transfer);
        $this->assertSame($transferResponse->txHash, $transfer->txid);
        $this->assertSame('0000000000000000', $transfer->paymentId);
        $this->assertGreaterThan(0, $transfer->timestamp);
        $this->assertSame(0, $transfer->amount);
        $this->assertSame('', $transfer->note);
        $this->assertSame(1, count($transfer->destinations));
        $this->assertSame(TestHelper::MAINNET_ADDRESS_1, $transfer->destinations[0]->address);
        $this->assertEquals(TransferType::OUTGOING, $transfer->type);
        $this->assertSame(0, $transfer->unlockTime);
        $this->assertSame(TestHelper::MAINNET_ADDRESS_1, $transfer->address);
        $this->assertSame(false, $transfer->doubleSpendSeen);
        //        $this->assertSame(61, $transfer->confirmations);
    }
}