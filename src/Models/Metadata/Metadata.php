<?php

namespace Hyde\Framework\Models\Metadata;

use Hyde\Framework\Contracts\AbstractPage;
use Hyde\Framework\Contracts\MetadataItemContract;
use Hyde\Framework\Helpers\Features;
use Hyde\Framework\Helpers\Meta;
use Hyde\Framework\Hyde;
use Hyde\Framework\Models\Pages\MarkdownPost;
use Hyde\Framework\Services\RssFeedService;

/**
 * @see \Hyde\Framework\Testing\Feature\MetadataTest
 */
class Metadata
{
    protected AbstractPage $page;

    public array $links = [];
    public array $metadata = [];
    public array $properties = [];
    public array $generics = [];

    public function __construct(AbstractPage $page)
    {
        $this->page = $page;
        $this->generate();
    }

    public function render(): string
    {
        return implode("\n", $this->get());
    }

    public function get(): array
    {
        return array_merge(
            $this->getPrefixedArray('links'),
            $this->getPrefixedArray('metadata'),
            $this->getPrefixedArray('properties'),
            $this->getPrefixedArray('generics')
        );
    }

    public function add(MetadataItemContract|string $item): static
    {
        if ($item instanceof LinkItem) {
            $this->links[$item->uniqueKey()] = $item;
        } elseif ($item instanceof MetadataItem) {
            $this->metadata[$item->uniqueKey()] = $item;
        } elseif ($item instanceof OpenGraphItem) {
            $this->properties[$item->uniqueKey()] = $item;
        } else {
            $this->generics[] = $item;
        }

        return $this;
    }

    protected function generate(): void
    {
        foreach (config('hyde.meta', []) as $item) {
            $this->add($item);
        }

        if (Features::sitemap()) {
            $this->add(Meta::link('sitemap', Hyde::url('sitemap.xml'), [
                'type' => 'application/xml', 'title' => 'Sitemap',
            ]));
        }

        if (Features::rss()) {
            $this->add(Meta::link('alternate', Hyde::url(RssFeedService::getDefaultOutputFilename()), [
                'type' => 'application/rss+xml', 'title' => RssFeedService::getDescription(),
            ]));
        }

        if ($this->page->has('canonicalUrl')) {
            $this->add(Meta::link('canonical', $this->page->get('canonicalUrl')));
        }

        if ($this->page->has('title')) {
            $this->add(Meta::name('twitter:title', $this->page->htmlTitle()));
            $this->add(Meta::property('title', $this->page->htmlTitle()));
        }

        if ($this->page instanceof MarkdownPost) {
            $this->addMetadataForMarkdownPost($this->page);
        }
    }

    protected function addMetadataForMarkdownPost(MarkdownPost $page): void
    {
        if ($page->has('description')) {
            $this->add(Meta::name('description', $page->get('description')));
        }

        if ($page->has('author')) {
            $this->add(Meta::name('author', $page->get('author')));
        }

        if ($page->has('category')) {
            $this->add(Meta::name('keywords', $page->get('category')));
        }

        if ($page->has('canonicalUrl')) {
            $this->add(Meta::property('url', $page->get('canonicalUrl')));
        }

        if ($page->has('title')) {
            $this->add(Meta::property('title', $page->get('title')));
        }

        if ($page->has('date')) {
            $this->add(Meta::property('og:article:published_time', $page->date->datetime));
        }

        if ($page->has('image')) {
            $this->add(Meta::property('image', $this->resolveImageLink($page->get('image'))));
        }

        $this->add(Meta::property('type', 'article'));
    }

    protected function getPrefixedArray(string $group): array
    {
        $array = [];
        foreach ($this->{$group} as $key => $value) {
            $array[$group.':'.$key] = $value;
        }

        return $array;
    }

    protected function resolveImageLink(string $image): string
    {
        // Since this is run before the page is rendered, we don't have the currentPage property
        // but since we know that this is for a blog post we know what the property will be
        // since Hyde does not currently support custom Blog post output directories.
        return str_starts_with($image, 'http') ? $image : "../$image";
    }
}
