<?php

namespace Hyde\Framework\Concerns\Markdown;

use function config;

/**
 * Global Markdown Feature Handler.
 *
 * @see HasConfigurableMarkdownFeatures for per-object configuration
 */
trait HasMarkdownFeatures
{
    public static function hasTableOfContents(): bool
    {
        return config('hyde.documentationPageTableOfContents.enabled', true);
    }
}
