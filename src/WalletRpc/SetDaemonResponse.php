<?php

declare(strict_types=1);

namespace MoneroIntegrations\MoneroRpc\WalletRpc;

use MoneroIntegrations\MoneroRpc\Trait\JsonSerializeBigInt;
use Square\Pjson\JsonDataSerializable;

/**
 * Connect the RPC server to a Monero daemon.
 */
class SetDaemonResponse implements JsonDataSerializable
{
    use JsonSerializeBigInt;
}
