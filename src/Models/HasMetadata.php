<?php

namespace Hyde\Framework\Models;

use Hyde\Framework\Hyde;
use JetBrains\PhpStorm\ArrayShape;

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
            $this->metadata->add('author', $this->matter['author']);
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

        // If there is an image, add it to the metadata
        // TODO: Add image to metadata
    }
}
