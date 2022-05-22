<?php

namespace Hyde\Framework\Concerns\Markdown;

/**
 * Global Markdown Feature Handler.
 *
 * @see HasConfigurableMarkdownFeatures for per-object configuration
 */
trait HasMarkdownFeatures
{
    public static function hasTableOfContents(): bool
    {
        return config('hyde.documentation_page_table_of_contents.enabled', true);
    }
}
