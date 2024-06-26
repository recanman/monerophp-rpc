<?php

declare(strict_types=1);

namespace MoneroIntegrations\MoneroRpc\WalletRpc\Model;

use MoneroIntegrations\MoneroRpc\Model\Address;
use MoneroIntegrations\MoneroRpc\Trait\JsonSerializeBigInt;
use Square\Pjson\Json;
use Square\Pjson\JsonDataSerializable;

class SubAddressAccountInformation implements JsonDataSerializable
{
    use JsonSerializeBigInt;

    /**
     * Index of the account.
     */
    #[Json('account_index')]
    public int $accountIndex;

    /**
     * Balance of the account (locked or unlocked).
     */
    #[Json]
    public int $balance;

    /**
     * Base64 representation of the first subaddress in the account.
     */
    #[Json('base_address')]
    public Address $baseAddress;

    /**
     * (Optional) Label of the account.
     */
    #[Json(omit_empty: true)]
    public ?string $label;

    /**
     * (Optional) Tag for filtering accounts.
     */
    #[Json(omit_empty: true)]
    public ?string $tag;

    /**
     * Unlocked balance for the account.
     */
    #[Json('unlocked_balance')]
    public int $unlockedBalance;

    public function __construct(
        int $accountIndex,
        int $balance,
        Address $baseAddress,
        int $unlockedBalance,
        ?string $label = null,
        ?string $tag = null,
    ) {
        $this->accountIndex = $accountIndex;
        $this->balance = $balance;
        $this->baseAddress = $baseAddress;
        $this->label = $label;
        $this->tag = $tag;
        $this->unlockedBalance = $unlockedBalance;
    }
}
