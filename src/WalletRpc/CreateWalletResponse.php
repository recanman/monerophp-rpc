<?php

declare(strict_types=1);

namespace RefRing\MoneroRpcPhp\WalletRpc;

use Square\Pjson\JsonSerialize;

/**
 * Create a new wallet. You need to have set the argument "--wallet-dir" when launching monero-wallet-rpc to make this work.
 */
class CreateWalletResponse
{
    use JsonSerialize;
}
