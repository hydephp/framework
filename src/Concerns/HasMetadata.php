<?php

namespace Hyde\Framework\Concerns;

use Hyde\Framework\Hyde;
use Hyde\Framework\Models\Metadata;
use Hyde\Framework\Services\AuthorService;
use JetBrains\PhpStorm\ArrayShape;

/**
 * Handle logic for Page models that have Metadata.
 * Metadata is used to create meta SEO tags.
 *
 * @see \Hyde\Framework\Models\Metadata
 * @see \Tests\Feature\Concerns\HasMetadataTest
 *
 * @todo Unify the $page property and handle metadata through it
 * @todo Only add blog post properties if the page is a blog post
 */
trait HasMetadata
{
    public ?Metadata $metadata = null;

    public function constructMetadata(): void
    {
        $this->metadata = new Metadata();

        $this->parseFrontMatterMetadata();
        $this->makeOpenGraphPropertiesForArticle();
    }

    #[ArrayShape(['name' => "\content"])]
    public function getMetadata(): array
    {
        if (! isset($this->metadata)) {
            return [];
        }

        return $this->metadata->metadata;
    }

    #[ArrayShape(['property' => 'content'])]
    public function getMetaProperties(): array
    {
        if (! isset($this->metadata)) {
            return [];
        }

        return $this->metadata->properties;
    }

    /**
     * Generate metadata from the front matter that can be used in standard <meta> tags.
     * This helper is page type agnostic and works with any kind of model having front matter.
     */
    protected function parseFrontMatterMetadata(): void
    {
        if (isset($this->matter['description'])) {
            $this->metadata->add('description', $this->matter['description']);
        }

        if (isset($this->matter['author'])) {
            $this->metadata->add('author', AuthorService::getAuthorName($this->matter['author']));
        }

        if (isset($this->matter['category'])) {
            $this->metadata->add('keywords', $this->matter['category']);
        }
    }

    /**
     * Generate opengraph metadata from front matter for an og:article such as a blog post.
     */
    protected function makeOpenGraphPropertiesForArticle(): void
    {
        $this->metadata->addProperty('og:type', 'article');

        if (Hyde::uriPath()) {
            $this->metadata->addProperty('og:url', Hyde::uriPath(Hyde::pageLink('posts/'.$this->slug.'.html')));
        }

        if (isset($this->matter['title'])) {
            $this->metadata->addProperty('og:title', $this->matter['title']);
        }

        if (isset($this->matter['date'])) {
            $date = date('c', strtotime($this->matter['date']));
            $this->metadata->addProperty('og:article:published_time', $date);
        }

        if (isset($this->matter['image'])) {
            if (is_string($this->matter['image'])) {
                $this->metadata->addProperty('og:image', $this->matter['image']);
            } else {
                if (isset($this->matter['image']['path'])) {
                    $this->metadata->addProperty('og:image', $this->matter['image']['path']);
                }
                if (isset($this->matter['image']['uri'])) {
                    $this->metadata->addProperty('og:image', $this->matter['image']['uri']);
                }
            }
        }
    }
}
