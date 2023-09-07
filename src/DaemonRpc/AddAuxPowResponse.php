<?php

declare(strict_types=1);

namespace RefRing\MoneroRpcPhp\DaemonRpc;

use RefRing\MoneroRpcPhp\Model\AuxPow;
use Square\Pjson\Json;
use Square\Pjson\JsonSerialize;

/**
 * Easily enable merge mining with Monero without requiring software that manually alters the extra field in the coinbase tx to include the merkle root of the aux blocks.Alias: *None*.
 */
class AddAuxPowResponse
{
    use JsonSerialize;

    #[Json('blocktemplate_blob')]
    public string $blocktemplateBlob;

    #[Json('blockhashing_blob')]
    public string $blockhashingBlob;

    #[Json('merkle_root')]
    public string $merkleRoot;

    #[Json('merkle_tree_depth')]
    public int $merkleTreeDepth;

    /** @var AuxPow[] */
    #[Json('aux_pow')]
    public array $auxPow;
}