<?php

namespace Hyde\Framework\Concerns;

trait HasSourceDirectory
{
    public static string $sourceDirectory;

    public function __construct()
    {
        if (! isset(static::$sourceDirectory)) {
            static::$sourceDirectory = static::$defaultSourceDirectory;
        }
    }
}