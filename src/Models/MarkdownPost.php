<?php

namespace Hyde\Framework\Models;

use Hyde\Framework\MarkdownPostParser;

class MarkdownPost extends MarkdownDocument
{
    use HasMetadata;

    public ?Image $image = null;
    public ?Metadata $metadata = null;
    public ?Datestring $date = null;

    public static string $sourceDirectory = '_posts';
    public static string $parserClass = MarkdownPostParser::class;
}
