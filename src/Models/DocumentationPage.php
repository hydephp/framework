<?php

namespace Hyde\Framework\Models;

/** @inheritDoc */
class DocumentationPage extends MarkdownDocument
{
    public string $title;
    public string $body;
    public string $slug;

    public function __construct(string $body, string $title, string $slug)
    {
        parent::__construct([], $body);
        $this->slug = $slug;
        $this->title = $title;
    }
}
