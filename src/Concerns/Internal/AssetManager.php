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
     * Return the Tailwind CDN if enabled.
     * @deprecated use tailwindCdn() instead
     */
    public static function tailwind(): string|false
    {
        return static::tailwindCdn();
    }

    /**
     * Return the Tailwind CDN if enabled.
     */
    public static function tailwindCdn(): string|false
    {
        return (new AssetService)->tailwindPath();
    }

    /**
     * Return the Hyde stylesheet.
     */
    public static function styles(): string
    {
        return (new AssetService)->stylePath();
    }

    /**
     * Return the Hyde scripts.
     */
    public static function scripts(): string
    {
        return (new AssetService)->scriptPath();
    }
}
