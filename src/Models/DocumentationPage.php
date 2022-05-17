<?php

namespace Hyde\Framework\Models;

use Hyde\Framework\Concerns\HasTableOfContents;
use Hyde\Framework\DocumentationPageParser;
use Hyde\Framework\Hyde;

class DocumentationPage extends MarkdownDocument
{
    use HasTableOfContents;

    public static string $sourceDirectory = '_docs';
    public static string $parserClass = DocumentationPageParser::class;

    public function __construct(array $matter, string $body, string $title = '', string $slug = '')
    {
        parent::__construct($matter, $body, $title, $slug);

        $this->constructTableOfContents();
    }

    public function getCurrentPagePath(): string
    {
        return  Hyde::docsDirectory().'/'.$this->slug;
    }
}
