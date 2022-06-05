<?php

namespace Hyde\Framework\Models;

use Hyde\Framework\Models\Parsers\MarkdownPageParser;
use Illuminate\Support\Collection;

class MarkdownPage extends MarkdownDocument
{
    public static string $sourceDirectory = '_pages';
    public static string $parserClass = MarkdownPageParser::class;
}
