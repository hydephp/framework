<?php

namespace Hyde\Framework;

use Hyde\Framework\Actions\ServiceActions\HasMarkdownFeatures;

/**
 * General interface for Markdown services.
 *
 * @see \Hyde\Framework\Services\MarkdownConverterService
 *
 * @method static hasTableOfContents()
 */
class Markdown
{
    use HasMarkdownFeatures;
}
