<?php

namespace Hyde\Framework\Concerns;

use Hyde\Framework\Helpers\Meta;
use Hyde\Framework\Hyde;
use Hyde\Framework\Models\MarkdownPost;
use Hyde\Framework\Services\RssFeedService;

/**
 * @see \Tests\Feature\Concerns\HasPageMetadataTest
 */
trait HasPageMetadata
{
    public function getCanonicalUrl(): string
    {
        return Hyde::uriPath(Hyde::pageLink($this->getCurrentPagePath().'.html'));
    }

    public function getDynamicMetadata(): array
    {
        $array = [];

        if ($this->canUseCanonicalUrl()) {
            $array[] = '<link rel="canonical" href="'.$this->getCanonicalUrl().'" />';
        }

        if ($this->canUseRssFeedlink()) {
            $array[] = '<link rel="alternate" type="application/rss+xml" title="'
            .RssFeedService::getTitle()
            .' RSS Feed" href="'
            .Hyde::uriPath(RssFeedService::getDefaultOutputFilename())
            .'" />';
        }

        if ($this instanceof MarkdownPost) {
            // Temporarily merge data with GeneratesPageMetadata trait for compatibility
            $array[] = "\n<!-- Blog Post Meta Tags -->";
            foreach ($this->getMetadata() as $name => $content) {
                $array[] = Meta::name($name, $content);
            }
            foreach ($this->getMetaProperties() as $property => $content) {
                $array[] = Meta::property($property, $content);
            }
        }

        return $array;
    }

    public function renderPageMetadata(): string
    {
        $dynamicMetadata = $this->getDynamicMetadata();

        return Meta::render(
            $dynamicMetadata
        );
    }

    public function canUseCanonicalUrl(): bool
    {
        return Hyde::uriPath() && isset($this->slug);
    }

    public function canUseRssFeedLink(): bool
    {
        if (RssFeedService::canGenerateFeed() && isset($this->slug)) {
            if ($this instanceof MarkdownPost) {
                return true;
            }

            if (str_starts_with($this->getCurrentPagePath(), 'post')) {
                return true;
            }

            if ($this->getCurrentPagePath() === 'index') {
                return true;
            }
        }

        return false;
    }
}
