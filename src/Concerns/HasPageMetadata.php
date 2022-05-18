<?php

namespace Hyde\Framework\Concerns;

use Hyde\Framework\Hyde;
use Hyde\Framework\Meta;
use Hyde\Framework\Models\MarkdownPost;

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

        if ($this instanceof MarkdownPost) {
            // Temporarly merge data with HasMetadata trait for compatibility
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
}
