<?php

namespace Hyde\Framework\Models;

/**
 * Intermediate object model created by the MarkdownFileService
 * and contains the Front Matter and Markdown body found in a document.
 */
class MarkdownDocument extends PageModel
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
     * Construct the class.
     *
     * @param  array  $matter
     * @param  string  $body
     */
    public function __construct(array $matter, string $body)
    {
        $this->matter = $matter;
        $this->body = $body;
    }
}
