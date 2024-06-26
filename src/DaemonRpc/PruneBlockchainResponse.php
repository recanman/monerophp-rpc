<?php

declare(strict_types=1);

namespace MoneroIntegrations\MoneroRpc\DaemonRpc;

use Square\Pjson\Json;
use MoneroIntegrations\MoneroRpc\Trait\JsonSerializeBigInt;
use Square\Pjson\JsonDataSerializable;

class PruneBlockchainResponse implements JsonDataSerializable
{
    use JsonSerializeBigInt;
    use DaemonStandardResponseFields;

    #[Json]
    public bool $pruned;

    /**
     * Blockheight at which pruning began.
     */
    #[Json('pruning_seed')]
    public int $pruningSeed;
}
