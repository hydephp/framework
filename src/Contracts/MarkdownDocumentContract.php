<?php

namespace Hyde\Framework\Contracts;

use Hyde\Framework\Models\Markdown;

interface MarkdownDocumentContract
{
    /**
     * Get the front matter object, or a value from within.
     *
     * @return \Hyde\Framework\Models\FrontMatter|mixed
     */
    public function matter(string $key = null, mixed $default = null): mixed;

    /**
     * Return the document's Markdown object.
     *
     * @return \Hyde\Framework\Models\Markdown
     */
    public function markdown(): Markdown;

    /**
     * Get the Markdown text body.
     *
     * @return string
     */
    public function body(): string;
}
