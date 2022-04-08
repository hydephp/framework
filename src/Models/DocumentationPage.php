<?php

namespace Hyde\Framework\Models;

/** @inheritDoc */
class DocumentationPage extends MarkdownDocument
{
    public string $title;
    public string $body;
    public string $slug;

    public function __construct(string $slug, string $title, string $body)
    {
        $this->slug = $slug;
        $this->title = $title;
        $this->body = $body;
    }
}
