<?php

namespace Hyde\Framework\Models;

use Hyde\Framework\MarkdownPostParser;

class MarkdownPost extends MarkdownDocument
{
    use HasMetadata;
    use HasDateString;

    public ?Image $image = null;

    public static string $sourceDirectory = '_posts';
    public static string $parserClass = MarkdownPostParser::class;

    public function __construct(array $matter, string $body, string $title = '', string $slug = '')
    {
        parent::__construct($matter, $body, $title, $slug);

        $this->constructMetadata();
        $this->constructDateString();
    }
}
