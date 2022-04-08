<?php

namespace Hyde\Framework\Models;

/**
 * The base class for all Markdown-based Page Models.
 * 
 * It is, in itself an intermediate object model created by the MarkdownFileService
 * and contains the Front Matter and Markdown body found in a document processed by the service.
 */
class MarkdownDocument
{
    /**
     * The Front Matter Array.
     *
     * @var array
     */
    public array $matter;

    /**
     * The Markdown Body String.
     *
     * @var string
     */
    public string $body;

    /**
     * The HTML Page Title.
     * @var string
     */
    public string $title;

    /**
     * The Page Slug (Basename).
     * @var string
     */
    public string $slug;

    /**
     * Construct the class.
     *
     * @param array $matter
     * @param string $body
     * @param string $title
     * @param string $slug
     */
    public function __construct(array $matter, string $body, string $title = '', string $slug = '')
    {
        $this->matter = $matter;
        $this->body = $body;
        $this->title = $title;
        $this->slug = $slug;
    }
}
