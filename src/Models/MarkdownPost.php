<?php

namespace Hyde\Framework\Models;

/** @inheritDoc */
class MarkdownPost extends MarkdownDocument
{
    public string $slug;

    public function __construct(array $matter, string $body, string $slug)
    {
        parent::__construct($matter, $body);
        $this->slug = $slug;
    }
}
