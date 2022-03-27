<?php

namespace Hyde\Framework\Models;

use JetBrains\PhpStorm\Pure;

/**
 * The basis for Markdown Blog Posts.
 */
class MarkdownPost extends MarkdownDocument
{
    /**
     * The Post Slug.
     *
     * @var string
     */
    public string $slug;

    /**
     * Construct the object.
     *
     * @param  array  $matter
     * @param  string  $body
     * @param  string  $slug
     */
    #[Pure]
 public function __construct(array $matter, string $body, string $slug)
 {
     parent::__construct($matter, $body);
     $this->slug = $slug;
 }
}
