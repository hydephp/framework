<?php

namespace Hyde\Framework\Concerns\Internal;

use Hyde\Framework\Services\AssetService;

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
     * @todo Refactor to load the service from the container.
     *
     * @return \Hyde\Framework\Services\AssetService
     */
    public static function assetManager(): AssetService
    {
        return new AssetService;
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
