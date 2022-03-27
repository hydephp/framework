<?php

namespace Hyde\Framework\Models;

/**
 * The basis for custom Markdown Pages.
 */
class MarkdownPage extends MarkdownDocument
{
    /**
     * The Page Title
     * @var string
     */
    public string $title;

    /**
     * The Post Slug
     * @var string
     */
    public string $slug;

    /**
     * Construct the object.
     *
     * @param string $slug
     * @param string $title
     * @param string $content
     */
    public function __construct(array $matter, string $body, string $slug, string $title)
    {
        $this->matter = $matter;
        $this->body = $body;
        $this->slug = $slug;
        $this->title = $title;
    }
}
