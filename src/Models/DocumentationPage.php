<?php

namespace Hyde\Framework\Models;

/**
 * A simple class that contains the body of a Documentation Page.
 */
class DocumentationPage extends MarkdownDocument
{
    /**
     * The Page Title.
     *
     * @var string
     */
    public string $title;

    /**
     * The Markdown body.
     *
     * @var string
     */
    public string $body;

    /**
     * The Post Slug.
     *
     * @var string
     */
    public string $slug;

    /**
     * Construct the object.
     *
     * @param  string  $slug
     * @param  string  $title
     * @param  string  $body
     */
    public function __construct(string $slug, string $title, string $body)
    {
        $this->slug = $slug;
        $this->title = $title;
        $this->body = $body;
    }
}
