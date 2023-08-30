<?php

declare(strict_types=1);

namespace RefRing\MoneroRpcPhp\DaemonRpc;

use Square\Pjson\Json;
use Square\Pjson\JsonSerialize;

/**
 * Check if an IP address is banned and for how long.Alias: *None*
 */
class BannedResponse
{
    use JsonSerialize;

    #[Json]
    public bool $banned;

    #[Json]
    public int $seconds;

    /**
     * General RPC error code. "OK" means everything looks good.
     */
    #[Json]
    public string $status;
}
