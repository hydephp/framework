<?php

namespace Hyde\Framework\Concerns;

use Hyde\Framework\Hyde;
use Hyde\Framework\Models\Metadata;
use JetBrains\PhpStorm\ArrayShape;


/**
 * Handle logic for Page models that have Metadata.
 * Metadata is used to create meta SEO tags.
 *
 * @see \Hyde\Framework\Models\Metadata
 */
trait HasMetadata
{
    public ?Metadata $metadata = null;

    public function constructMetadata(): void
    {
        $this->metadata = new Metadata();
        $this->makeMetadata();
        $this->makeMetaProperties();
    }

    #[ArrayShape(['name' => "\content"])]
 public function getMetadata(): array
 {
     if (! isset($this->metadata)) {
         return [];
     }

     return $this->metadata->metadata;
 }

    #[ArrayShape(['property' => "\content"])]
    public function getMetaProperties(): array
    {
        if (! isset($this->metadata)) {
            return [];
        }

        return $this->metadata->properties;
    }

    protected function makeMetadata(): void
    {
        if (isset($this->matter['description'])) {
            $this->metadata->add('description', $this->matter['description']);
        }

        // Add author if it exists
        if (isset($this->matter['author'])) {
            $this->metadata->add('author', $this->getAuthor($this->matter['author']));
        }

        // Add keywords if it exists
        if (isset($this->matter['category'])) {
            $this->metadata->add('keywords', $this->matter['category']);
        }
    }

    protected function makeMetaProperties(): void
    {
        $this->metadata->addProperty('og:type', 'article');

        if (Hyde::uriPath()) {
            $this->metadata->addProperty('og:url', Hyde::uriPath('posts/'.$this->slug));
        }

        // Add title if it exists
        if (isset($this->matter['title'])) {
            $this->metadata->addProperty('og:title', $this->matter['title']);
        }

        // Add date if it exists
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

    protected function getAuthor(string|array $author): string
    {
        if (is_string($author)) {
            return $author;
        }

        return $author['username'] ?? $author['name'] ?? 'Guest';
    }
}
