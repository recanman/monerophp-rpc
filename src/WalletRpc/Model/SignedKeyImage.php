<?php

declare(strict_types=1);

namespace MoneroIntegrations\MoneroRpc\WalletRpc\Model;

use MoneroIntegrations\MoneroRpc\Trait\JsonSerializeBigInt;
use Square\Pjson\Json;
use Square\Pjson\JsonDataSerializable;

class SignedKeyImage implements JsonDataSerializable
{
    use JsonSerializeBigInt;

    #[Json('key_image')]
    public string $keyImage;

    #[Json]
    public string $signature;

    public function __construct(string $keyImage, string $signature)
    {
        $this->keyImage = $keyImage;
        $this->signature = $signature;
    }
}
