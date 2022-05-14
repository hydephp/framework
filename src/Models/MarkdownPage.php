<?php

namespace Hyde\Framework\Models;

use Hyde\Framework\MarkdownPageParser;

class MarkdownPage extends MarkdownDocument
{
    public static string $sourceDirectory = config('hyde.findMarkdownPagesIn', '_pages');
    public static string $parserClass = MarkdownPageParser::class;
}
