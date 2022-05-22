<?php

namespace Hyde\Framework\Concerns\Internal;

use Hyde\Framework\Contracts\AssetServiceContract;

/**
 * Offloads asset related methods for the Hyde Facade.
 *
 * @see \Hyde\Framework\Hyde
 */
trait AssetManager
{
    /**
     * Get the asset service instance.
     *
     * @return \Hyde\Framework\Contracts\AssetServiceContract
     */
    public static function assetManager(): AssetServiceContract
    {
        return app(AssetServiceContract::class);
    }

    /**
     * Return the Hyde stylesheet.
     */
    public static function styles(): string|false
    {
        return config('hyde.loadHydeAssetsUsingCDN', true) ? static::assetManager()->stylePath() : false;
    }

    /**
     * Return the Hyde scripts.
     */
    public static function scripts(): string
    {
        return config('hyde.loadHydeAssetsUsingCDN', true) ? static::assetManager()->scriptPath() : false;
    }
}
