<?php

namespace Hyde\Framework\Concerns;

use Hyde\Framework\Hyde;
use Hyde\Framework\Meta;

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
