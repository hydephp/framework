<?php

declare(strict_types=1);

namespace Hyde\Framework\Features\Metadata;

use Hyde\Facades\Meta;
use Hyde\Pages\Concerns\HydePage;
use Hyde\Pages\MarkdownPost;

class PageMetadataBag extends MetadataBag
{
    protected HydePage $page;

    public function __construct(HydePage $page)
    {
        $this->page = $page;

        $this->generate();
    }

    protected function generate(): void
    {
        $this->addDynamicPageMetadata($this->page);
    }

    protected function addDynamicPageMetadata(HydePage $page): void
    {
        if ($page->has('canonicalUrl')) {
            $this->add(Meta::link('canonical', $page->data('canonicalUrl')));
        }

        if ($page->has('title')) {
            $this->add(Meta::name('twitter:title', $page->htmlTitle()));
            $this->add(Meta::property('title', $page->htmlTitle()));
        }

        if ($page instanceof MarkdownPost) {
            $this->addMetadataForMarkdownPost($page);
        }
    }

    protected function addMetadataForMarkdownPost(MarkdownPost $page): void
    {
        $this->addPostMetadataIfExists($page, 'description');
        $this->addPostMetadataIfExists($page, 'author');
        $this->addPostMetadataIfExists($page, 'category', 'keywords');
        $this->addPostMetadataIfExists($page, 'canonicalUrl', 'url');

        if ($page->has('canonicalUrl')) {
            $this->add(Meta::property('url', $page->data('canonicalUrl')));
        }

        if ($page->has('date')) {
            $this->add(Meta::property('og:article:published_time', $page->date->datetime));
        }

        if ($page->has('image')) {
            $this->add(Meta::property('image', $this->resolveImageLink((string) $page->data('image'))));
        }

        $this->add(Meta::property('type', 'article'));
    }

    protected function addPostMetadataIfExists(MarkdownPost $page, string $property, ?string $name = null): void
    {
        if ($page->has($property)) {
            $this->add(Meta::name($name ?? $property, (string) $page->data($property)));
        }
    }

    protected function resolveImageLink(string $image): string
    {
        // Since this is run before the page is rendered, we don't have the currentPage property.
        // So we need to run some of the same calculations here to resolve the image path link.
        return str_starts_with($image, 'http') ? $image
            : str_repeat('../', substr_count(MarkdownPost::outputDirectory().'/'.$this->page->identifier, '/')).$image;
    }
}
