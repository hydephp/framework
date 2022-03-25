<?php

namespace Hyde\Framework\Models;

use Hyde\Framework\Hyde;
use JetBrains\PhpStorm\Pure;

/**
 * A simple class that contains the content of a basic Markdown Page.
 */
class MarkdownPage
{
    /**
     * The Page Title
     * @var string
     */
    public string $title;

    /**
     * The Markdown Content
     * @var string
     */
    public string $content;

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
    public function __construct(string $slug, string $title, string $content)
    {
        $this->slug = $slug;
        $this->title = $title;
        $this->content = $content;
    }
}
