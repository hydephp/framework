<?php

namespace Hyde\Framework\Models;

/**
 * The basis for Markdown Blog Posts.
 */
class MarkdownPost extends MarkdownDocument
{
    /**
     * The Post slug
     * @var string
     */
    public string $slug;

    /**
     * Construct the object.
     *
     * @param array $matter
     * @param string $body
     * @param string $slug
     */
    public function __construct(array $matter, string $body, string $slug)
    {
        $this->matter = $matter;
        $this->body = $body;
        $this->slug = $slug;
    }
}
