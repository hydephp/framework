<?php

namespace Hyde\Framework\Core;

/**
 * Define where the source content files are located.
 * This is then used for Hyde auto-discovery.
 * Returned paths must be relative to the project root. e.g. '_posts', 'src/posts', etc.
 */
interface SourceLocationManager
{
    /**
     * Return the directory BladePages are stored in.
     * @see \Hyde\Framework\Models\BladePage
     */
    public function findBladePagesIn(): string;

    /**
     * Return the directory MarkdownPages are stored in.
     * @see \Hyde\Framework\Models\MarkdownPage
     */
    public function findMarkdownPagesIn(): string;

    /**
     * Return the directory MarkdownPosts are stored in.
     * @see \Hyde\Framework\Models\MarkdownPost
     */
    public function findMarkdownPostsIn(): string;

    /**
     * Return the directory DocumentationPages are stored in.
     * @see \Hyde\Framework\Models\DocumentationPage
     */
    public function findDocumentationPagesIn(): string;
}