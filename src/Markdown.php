<?php

namespace Hyde\Framework;

use Hyde\Framework\Actions\ServiceActions\HasMarkdownFeatures;

/**
 * General interface for Markdown services.
 *
 * @see \Hyde\Framework\Services\MarkdownConverterService
 */
class Markdown
{
    use HasMarkdownFeatures;
}
