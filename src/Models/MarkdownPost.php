<?php

namespace Hyde\Framework\Models;

use Hyde\Framework\Concerns\HasAuthor;
use Hyde\Framework\Concerns\HasDateString;
use Hyde\Framework\Concerns\HasFeaturedImage;
use Hyde\Framework\Concerns\HasMetadata;
use Hyde\Framework\MarkdownPostParser;

class MarkdownPost extends MarkdownDocument
{
    use HasAuthor;
    use HasMetadata;
    use HasDateString;
    use HasFeaturedImage;

    public ?string $category;

    public static string $sourceDirectory = '_posts';
    public static string $parserClass = MarkdownPostParser::class;

    public function __construct(array $matter, string $body, string $title = '', string $slug = '')
    {
        parent::__construct($matter, $body, $title, $slug);

        $this->constructAuthor();
        $this->constructMetadata();
        $this->constructDateString();
        $this->constructFeaturedImage();

        $this->category = $this->matter['category'] ?? null;
    }
    
    public function getCurrentPagePath(): string
    {
        return 'posts/'.$this->slug;
    }
}
