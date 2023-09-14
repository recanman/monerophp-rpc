<?php

declare(strict_types=1);

namespace RefRing\MoneroRpcPhp\WalletRpc;

use RefRing\MoneroRpcPhp\Request\ParameterInterface;
use RefRing\MoneroRpcPhp\Request\RpcRequest;
use Square\Pjson\Json;
use Square\Pjson\JsonSerialize;

/**
 * Retrieves entries from the address book.
 */
class GetAddressBookRequest implements ParameterInterface
{
    use JsonSerialize;

    /**
     * @var int[] indices of the requested address book entries
     */
    #[Json]
    public array $entries;


    /**
     * @param int[] $entries
     */
    public static function create(array $entries): RpcRequest
    {
        $self = new self();
        $self->entries = $entries;
        return new RpcRequest('get_address_book', $self);
    }
}
