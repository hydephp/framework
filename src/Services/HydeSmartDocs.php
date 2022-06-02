<?php

namespace Hyde\Framework\Services;

/**
 * Class to make Hyde documentation pages smarter,
 * allowing for rich and dynamic content.
 */
class HydeSmartDocs
{
    public static function isEnabled(): bool
    {
        return config('docs.smart_docs', true);
    }
}