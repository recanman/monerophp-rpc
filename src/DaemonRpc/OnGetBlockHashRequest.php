<?php

declare(strict_types=1);

namespace MoneroIntegrations\MoneroRpc\DaemonRpc;

use MoneroIntegrations\MoneroRpc\Request\ParameterInterface;
use MoneroIntegrations\MoneroRpc\Request\RpcRequest;
use MoneroIntegrations\MoneroRpc\Trait\CollectionTrait;

/**
 * Look up a block's hash by its height.
 * @implements \IteratorAggregate<int>
 */
class OnGetBlockHashRequest implements ParameterInterface, \IteratorAggregate
{
    use CollectionTrait;

    public static function create(int $blockHeight): RpcRequest
    {
        $self = new self();
        $self->values = [$blockHeight];
        return new RpcRequest('on_get_block_hash', $self);
    }
}
