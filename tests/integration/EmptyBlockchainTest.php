<?php

declare(strict_types=1);

namespace RefRing\MoneroRpcPhp\Tests\integration;

use PHPUnit\Framework\TestCase;
use RefRing\MoneroRpcPhp\DaemonRpc\GetBlockHeaderByHashResponse;
use RefRing\MoneroRpcPhp\DaemonRpc\GetBlockHeaderByHeightResponse;
use RefRing\MoneroRpcPhp\DaemonRpc\GetBlockTemplateResponse;
use RefRing\MoneroRpcPhp\DaemonRpc\GetLastBlockHeaderResponse;
use RefRing\MoneroRpcPhp\Exception\InvalidAddressException;
use RefRing\MoneroRpcPhp\Exception\InvalidBlockHashException;
use RefRing\MoneroRpcPhp\Exception\InvalidBlockHeightException;
use RefRing\MoneroRpcPhp\Exception\InvalidBlockHeightRangeException;
use RefRing\MoneroRpcPhp\Exception\InvalidReservedSizeException;
use RefRing\MoneroRpcPhp\Model\BlockHeader;
use RefRing\MoneroRpcPhp\RegtestRpcClient;
use RefRing\MoneroRpcPhp\Tests\TestHelper;

final class EmptyBlockchainTest extends TestCase
{
    private static RegtestRpcClient $regtestRpcClient;

    public static function setUpBeforeClass(): void
    {
        $httpClient = new \GuzzleHttp\Client();
        self::$regtestRpcClient = new RegtestRpcClient($httpClient, 'http://127.0.0.1:18081/json_rpc');
    }

    public function testBlockCountHeight(): void
    {
        $count = self::$regtestRpcClient->getBlockCount();
        $this->assertSame(1, $count->count);
    }

    public function testGetBlockHashInvalidHeight(): void
    {
        $this->expectException(InvalidBlockHeightException::class);
//        $this->expectExceptionMessage('Invalid height 10 supplied');
        self::$regtestRpcClient->onGetBlockHash(10);
    }

    public function testGenesisHash(): void
    {
        $blockHash = self::$regtestRpcClient->onGetBlockHash(0);
        $this->assertSame(TestHelper::GENESIS_BLOCK_HASH, (string) $blockHash);
    }

    public function testGetBlockTemplate(): void
    {
        $address = TestHelper::MAINNET_ADDRESS;

        $expectedBlockTemplate = new GetBlockTemplateResponse();
        $expectedBlockTemplate->difficulty = 1;
        $expectedBlockTemplate->height = 1;
        $expectedBlockTemplate->expectedReward = 35184338534400;
        $expectedBlockTemplate->prevHash = TestHelper::GENESIS_BLOCK_HASH;
        $expectedBlockTemplate->untrusted = false;
        $expectedBlockTemplate->difficultyTop64 = 0;
        $expectedBlockTemplate->nextSeedHash = '';
        $expectedBlockTemplate->seedHash = TestHelper::GENESIS_BLOCK_HASH;
        $expectedBlockTemplate->seedHeight = 0;
        $expectedBlockTemplate->status = 'OK';
        $expectedBlockTemplate->wideDifficulty = '0x1';
        $expectedBlockTemplate->blockhashingBlob = '';
        $expectedBlockTemplate->blocktemplateBlob = '';
        $expectedBlockTemplate->reservedOffset = 10;

        $blockTemplate = self::$regtestRpcClient->getBlockTemplate($address, 60);

        // The fields are non-deterministic so overwrite for the test
        $blockTemplate->blockhashingBlob = '';
        $blockTemplate->blocktemplateBlob = '';
        $blockTemplate->reservedOffset = 10;

        $this->assertEquals($expectedBlockTemplate, $blockTemplate);
    }

    public function testBlockTemplateErrorInvalidSize(): void
    {
        $this->expectException(InvalidReservedSizeException::class);
        $address = TestHelper::MAINNET_ADDRESS;
        self::$regtestRpcClient->getBlockTemplate($address, 256);
    }

    public function testBlockTemplateErrorInvalidAddress(): void
    {
        $this->expectException(InvalidAddressException::class);
        $address = 'xxx';
        self::$regtestRpcClient->getBlockTemplate($address, 10);
    }

    private function getGenesisBlockHeader(): BlockHeader
    {
        return new BlockHeader(80,80, 1, 0, 0,
            1, 0, TestHelper::GENESIS_BLOCK_HASH, 0, 80, 1, 'c88ce9783b4f11190d7b9c17a69c1c52200f9faaee8e98dd07e6811175177139',
            0, 10000, 0, false, '', '0000000000000000000000000000000000000000000000000000000000000000',
            17592186044415, 0, '0x1', '0x1');
    }

    public function testLastBlockHeader(): void
    {
        $expected = new GetLastBlockHeaderResponse();
        $expected->untrusted = false;
        $expected->credits = 0;
        $expected->topHash = '';
        $expected->status = 'OK';
        $expected->blockHeader = $this->getGenesisBlockHeader();

        $blockHeader = self::$regtestRpcClient->getLastBlockHeader();
        $this->assertEquals($expected, $blockHeader);
    }

    public function testGetBLockHeaderByHash(): void
    {
        $expected = new GetBlockHeaderByHashResponse();
        $expected->untrusted = false;
        $expected->credits = 0;
        $expected->topHash = '';
        $expected->status = 'OK';
        $expected->blockHeader = $this->getGenesisBlockHeader();

        $blockHeader = self::$regtestRpcClient->getBlockHeaderByHash(TestHelper::GENESIS_BLOCK_HASH);
        $this->assertEquals($expected, $blockHeader);
    }

    public function testGetBlockHeaderByHashErrorNotFoundEmpty(): void
    {
        $this->expectException(InvalidBlockHashException::class);
        self::$regtestRpcClient->getBlockHeaderByHash('0000000000000000000000000000000000000000000000000000000000000000');
    }

    public function testGetBlockHeaderByHashErrorNotFound(): void
    {
        $this->expectException(InvalidBlockHashException::class);
        self::$regtestRpcClient->getBlockHeaderByHash('4444444444444444444444444444444444444444444444444444444444444444');
    }

    public function testGetBlockHeaderByHeight(): void
    {
        $expected = new GetBlockHeaderByHeightResponse();
        $expected->untrusted = false;
        $expected->credits = 0;
        $expected->topHash = '';
        $expected->status = 'OK';
        $expected->blockHeader = $this->getGenesisBlockHeader();

        $blockHeader = self::$regtestRpcClient->getBlockHeaderByHeight(0);
        $this->assertEquals($expected, $blockHeader);
    }

    public function testGetBlockHeaderByHeightError(): void
    {
        $this->expectException(InvalidBlockHeightException::class);
        self::$regtestRpcClient->getBlockHeaderByHeight(10);
    }

    public function testGetBlockHeaderRange(): void
    {
        $blockHeaderList = self::$regtestRpcClient->getBlockHeadersRange(0,0);
        $this->assertEquals([$this->getGenesisBlockHeader()], $blockHeaderList->headers);
    }

    public function testGetBlockHeaderRangeError(): void
    {
        $this->expectException(InvalidBlockHeightRangeException::class);
        self::$regtestRpcClient->getBlockHeadersRange(0,10);
    }

    public function testGetBlockHeaderRangeErrorNonZero(): void
    {
        $this->expectException(InvalidBlockHeightRangeException::class);
        self::$regtestRpcClient->getBlockHeadersRange(10,20);
    }
}
