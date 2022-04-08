<?php

namespace Hyde\Framework\Models;

/** @inheritDoc */
class MarkdownPost extends MarkdownDocument
{
    public string $title;
    public string $slug;

    public function __construct(array $matter, string $body, string $title, string $slug)
    {
        parent::__construct($matter, $body);
        $this->title = $title;
        $this->slug = $slug;
    }
}
