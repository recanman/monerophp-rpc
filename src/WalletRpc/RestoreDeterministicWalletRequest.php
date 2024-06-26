<?php

declare(strict_types=1);

namespace MoneroIntegrations\MoneroRpc\WalletRpc;

use MoneroIntegrations\MoneroRpc\Request\ParameterInterface;
use MoneroIntegrations\MoneroRpc\Request\RpcRequest;
use Square\Pjson\Json;
use MoneroIntegrations\MoneroRpc\Trait\JsonSerializeBigInt;
use Square\Pjson\JsonDataSerializable;

/**
 * Create and open a wallet on the RPC server from an existing mnemonic phrase and close the currently open wallet.
 */
class RestoreDeterministicWalletRequest implements ParameterInterface, JsonDataSerializable
{
    use JsonSerializeBigInt;

    /**
     * Name of the wallet.
     */
    #[Json]
    public string $filename;

    /**
     * Password of the wallet.
     */
    #[Json]
    public string $password;

    /**
     * Mnemonic phrase of the wallet to restore.
     */
    #[Json]
    public string $seed;

    /**
     * Block height to restore the wallet from (.
     * When omitted the default value is 0
     */
    #[Json('restore_height', omit_empty: true)]
    public ?int $restoreHeight;

    /**
     * Language of the mnemonic phrase in case the old language is invalid.
     */
    #[Json(omit_empty: true)]
    public ?string $language;

    /**
     * Offset used to derive a new seed from the given mnemonic to recover a secret wallet from the mnemonic phrase.
     */
    #[Json('seed_offset', omit_empty: true)]
    public ?string $seedOffset;

    /**
     * Whether to save the currently open RPC wallet before closing it (.
     * When omitted the default value is true
     */
    #[Json('autosave_current', omit_empty: true)]
    public ?bool $autosaveCurrent;

    public static function create(
        string $filename,
        string $password,
        string $seed,
        ?int $restoreHeight = 0,
        ?string $language = null,
        ?string $seedOffset = null,
        ?bool $autosaveCurrent = null,
    ): RpcRequest {
        $self = new self();
        $self->filename = $filename;
        $self->password = $password;
        $self->seed = $seed;
        $self->restoreHeight = $restoreHeight;
        $self->language = $language;
        $self->seedOffset = $seedOffset;
        $self->autosaveCurrent = $autosaveCurrent;
        return new RpcRequest('restore_deterministic_wallet', $self);
    }
}
