<?php

namespace Hyde\Framework\Models;

use Hyde\Framework\MarkdownPostParser;

class MarkdownPost extends MarkdownDocument
{
    public static string $sourceDirectory = '_posts';
    public static string $parserClass = MarkdownPostParser::class;
}
