<?php

declare(strict_types=1);

namespace RefRing\MoneroRpcPhp\WalletRpc;

use RefRing\MoneroRpcPhp\Request\ParameterInterface;
use RefRing\MoneroRpcPhp\Request\RpcRequest;
use Square\Pjson\Json;
use Square\Pjson\JsonSerialize;

/**
 * Refresh a wallet after openning.Alias: *None*.
 */
class RefreshRequest implements ParameterInterface
{
    use JsonSerialize;

    /**
     * (Optional) The block height from which to start refreshing. Passing no value or a value less than the last block scanned by the wallet refreshes from the last block scanned.
     */
    #[Json('start_height', omit_empty: true)]
    public ?int $startHeight;


    public static function create(?int $startHeight = null): RpcRequest
    {
        $self = new self();
        $self->startHeight = $startHeight;
        return new RpcRequest('refresh', $self);
    }
}
