<?php

namespace Hyde\Framework\Concerns\Internal;

use Hyde\Framework\Services\AssetService;

/**
 * AssetManager for the Hyde Facade.
 */
trait AssetManager
{
    /**
     * Return the Tailwind CDN if enabled.
     */
    public static function tailwind(): string|false
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
