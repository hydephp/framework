<?php

namespace Hyde\Framework\Models;

use Hyde\Framework\DocumentationPageParser;

class DocumentationPage extends MarkdownDocument
{
    public static string $sourceDirectory = '_docs';
    public static string $parserClass = DocumentationPageParser::class;
}
