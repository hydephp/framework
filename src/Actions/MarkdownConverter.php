<?php

namespace Hyde\Framework\Actions;

use Hyde\Framework\Services\MarkdownConverterService;

/**
 * Converts Markdown into HTML.
 */
class MarkdownConverter
{
    /**
     * Parse the Markdown into HTML.
     *
     * @param  string  $markdown
     * @return string $html
     */
    public static function parse(string $markdown): string
    {
        return (new MarkdownConverterService($markdown))->parse();
    }
}
