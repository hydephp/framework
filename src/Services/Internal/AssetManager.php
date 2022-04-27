<?php

namespace Hyde\Framework\Services\Internal;

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
        return config('hyde.loadTailwindFromCDN')
            ? 'https://cdn.jsdelivr.net/gh/hydephp/hydefront@v1.3.1/dist/app.css'
            : false;
    }

    /**
     * Return the Hyde stylesheet.
     */
    public static function styles(): string
    {
        return 'https://cdn.jsdelivr.net/gh/hydephp/hydefront@v1.3.1/dist/hyde.css';
    }

    /**
     * Return the Hyde scripts.
     */
    public static function scripts(): string
    {
        return 'https://cdn.jsdelivr.net/gh/hydephp/hydefront@v1.3.1/dist/hyde.js';
    }
}