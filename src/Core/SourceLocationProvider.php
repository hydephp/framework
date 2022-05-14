<?php

namespace Hyde\Framework\Core;

class SourceLocationProvider implements SourceLocationManager
{

    /**
     * @inheritDoc
     */
    public function findBladePagesIn(): string
    {
        return '_pages';
    }

    /**
     * @inheritDoc
     */
    public function findMarkdownPagesIn(): string
    {
        return '_pages';
    }

    /**
     * @inheritDoc
     */
    public function findMarkdownPostsIn(): string
    {
        return '_posts';
    }

    /**
     * @inheritDoc
     */
    public function findDocumentationPagesIn(): string
    {
        return '_docs';
    }
}