<?php

namespace Hyde\Framework;

use Hyde\Framework\Models\Image;
use Hyde\Framework\Models\MarkdownPost;
use Hyde\Framework\Models\Metadata;
use Hyde\Framework\Services\MarkdownFileService;

class MarkdownPostParser extends AbstractPageParser
{
    protected string $pageModel = MarkdownPost::class;
    protected string $slug;

    public array $matter;
    public string $body;
    public string $title;

    public function execute(): void
    {
        $document = (new MarkdownFileService(
            Hyde::path("_posts/$this->slug.md")
        ))->get();

        $this->matter = array_merge($document->matter, [
            'slug' => $this->slug,
        ]);

        if (isset($document->matter['title'])) {
            $this->title = $document->matter['title'];
        } else {
            $this->title = Hyde::titleFromSlug($this->slug);
        }

        $this->body = $document->body;
    }

    public function get(): MarkdownPost
    {
        $post = new MarkdownPost(
            matter: $this->matter,
            body: $this->body,
            title: $this->title,
            slug: $this->slug
        );

        $post->image = $this->getImage();
        $post->metadata = $this->getMetadata();

        return $post;
    }

    protected function getImage(): Image|null
    {
        return null;
    }

    protected function getMetadata(): Metadata
    {
        $metadata = new Metadata();

        // Add description if it exists
        if (isset($this->matter['description'])) {
            $metadata->add('description', $this->matter['description']);
        }
        
        // Add author if it exists
        if (isset($this->matter['author'])) {
            $metadata->add('author', $this->matter['author']);
        }

        // Add keywords if it exists
        if (isset($this->matter['category'])) {
            $metadata->add('keywords', $this->matter['category']);
        }

        $metadata->addProperty('og:type', 'article');
        $metadata->addProperty('og:title', $this->title);

        // If there is an image, add it to the metadata
        // TODO: Add image to metadata

        // Add date if it exists
        if (isset($this->matter['date'])) {
            $date = date('c', strtotime($this->matter['date']));
            $metadata->addProperty('og:article:published_time', $date);
        }

        if (Hyde::uriPath()) {
            $metadata->addProperty('og:url', Hyde::uriPath('posts/' . $this->slug));
        }

        return $metadata;
    }
}
