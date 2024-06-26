<?php

declare(strict_types=1);

namespace MoneroIntegrations\MoneroRpc\WalletRpc;

use MoneroIntegrations\MoneroRpc\Model\Amount;
use MoneroIntegrations\MoneroRpc\Trait\JsonSerializeBigInt;
use Square\Pjson\Json;
use Square\Pjson\JsonDataSerializable;

/**
 * Proves a wallet has a disposable reserve using a signature.
 */
class CheckReserveProofResponse implements JsonDataSerializable
{
    use JsonSerializeBigInt;

    /**
     * States if the inputs proves the reserve.
     */
    #[Json]
    public bool $good;

    /**
     * Amount (in piconero) of the total that has been spent.
     */
    #[Json]
    public Amount $spent;

    /**
     * Total amount (in piconero) of the reserve that was proven.
     */
    #[Json]
    public Amount $total;
}
