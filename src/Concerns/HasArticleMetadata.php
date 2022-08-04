<?php

namespace Hyde\Framework\Concerns;

use Hyde\Framework\Contracts\RouteContract;
use Hyde\Framework\Hyde;

/**
 * Generates article metadata for a MarkdownPost.
 *
 * @see \Hyde\Framework\Models\Metadata
 * @see \Hyde\Framework\Testing\Feature\Concerns\HasArticleMetadataTest
 */
trait HasArticleMetadata
{
    public array $metadata = [];
    public array $properties = [];

    abstract public function getRoute(): RouteContract;

    protected function constructMetadata(): void
    {
        $this->parseFrontMatterMetadata();

        $this->makeOpenGraphPropertiesForArticle();
    }

    public function getMetadata(): array
    {
        return $this->metadata;
    }

    public function getMetaProperties(): array
    {
        return $this->properties;
    }

    /**
     * Generate metadata from the front matter that can be used in standard <meta> tags.
     * This helper is page type agnostic and works with any kind of model having front matter.
     */
    protected function parseFrontMatterMetadata(): void
    {
        if ($this->matter('description') !== null) {
            $this->metadata['description'] = $this->matter('description');
        }

        if ($this->matter('author') !== null) {
            $this->metadata['author'] = $this->getAuthorName($this->matter('author'));
        }

        if ($this->matter('category') !== null) {
            $this->metadata['keywords'] = $this->matter('category');
        }
    }

    /**
     * Generate opengraph metadata from front matter for an og:article such as a blog post.
     */
    protected function makeOpenGraphPropertiesForArticle(): void
    {
        $this->properties['og:type'] = 'article';
        if (Hyde::hasSiteUrl()) {
            $this->properties['og:url'] = $this->getRoute()->getQualifiedUrl();
        }

        if ($this->matter('title') !== null) {
            $this->properties['og:title'] = $this->matter('title');
        }

        if ($this->matter('date') !== null) {
            $this->properties['og:article:published_time'] = date('c', strtotime($this->matter('date')));
        }

        if ($this->matter('image') !== null) {
            $this->setImageMetadata();
        }
    }

    /**
     * Parse the author name string from front matter with support for both flat and array notation.
     *
     * @param  string|array  $author
     * @return string
     */
    protected function getAuthorName(string|array $author): string
    {
        if (is_string($author)) {
            return $author;
        }

        return $author['name'] ?? $author['username'] ?? 'Guest';
    }

    protected function setImageMetadata(): void
    {
        if (is_string($this->matter('image'))) {
            $this->properties['og:image'] = $this->matter('image');
        } else {
            if (isset($this->matter('image')['path'])) {
                $this->properties['og:image'] = $this->matter('image')['path'];
            }
            if (isset($this->matter('image')['uri'])) {
                $this->properties['og:image'] = $this->matter('image')['uri'];
            }
        }
    }
}
