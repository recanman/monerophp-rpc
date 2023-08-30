<?php

declare(strict_types=1);

namespace RefRing\MoneroRpcPhp\WalletRpc;

use RefRing\MoneroRpcPhp\Model\QueryKeyType;
use RefRing\MoneroRpcPhp\Request\ParameterInterface;
use RefRing\MoneroRpcPhp\Request\RpcRequest;
use Square\Pjson\Json;
use Square\Pjson\JsonSerialize;

/**
 * Return the spend or view private key.Alias: *None*.
 */
class QueryKeyRequest implements ParameterInterface
{
    use JsonSerialize;

    /**
     * Which key to retrieve: "mnemonic" - the mnemonic seed (older wallets do not have one) OR "view_key" - the view key OR "spend_key".
     */
    #[Json('key_type')]
    public QueryKeyType $keyType;


    public static function create(QueryKeyType $keyType): RpcRequest
    {
        $self = new self();
        $self->keyType = $keyType;
        return new RpcRequest('query_key', $self);
    }
}
