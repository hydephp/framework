<?php

namespace Hyde\Framework\Models;

use JetBrains\PhpStorm\Pure;

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
     * @param array $matter
     * @param string $body
     * @param string $slug
     * @param string $title
     */
    #[Pure] public function __construct(array $matter, string $body, string $slug, string $title)
    {
        parent::__construct($matter, $body);
        $this->title = $title;
        $this->slug = $slug;
    }
}
