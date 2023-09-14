<?php

declare(strict_types=1);

namespace RefRing\MoneroRpcPhp\WalletRpc;

use Square\Pjson\JsonSerialize;

/**
 * Stop mining in the Monero daemon.
 */
class StopMiningResponse
{
    use JsonSerialize;
}
