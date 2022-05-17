<?php

namespace Hyde\Framework\Contracts;

use Hyde\Framework\Hyde;
use Hyde\Framework\Meta;

/**
 * To ensure compatability with the Hyde Framework,
 * all Page Models must extend this class.
 *
 * Markdown-based Pages should extend MarkdownDocument.
 * 
 * @todo Extract metadata helpers to concern
 */
abstract class AbstractPage
{
    public static string $sourceDirectory;
    public static string $fileExtension;
    public static string $parserClass;

    public function getCurrentPagePath(): string
    {
        return $this->slug;
    }

    public function renderPageMetadata(): string
    {
        $dynamicMetadata = $this->getDynamicMetadata();

        return Meta::render(
            $dynamicMetadata 
        );
    }

    protected function getDynamicMetadata(): array
    {
        $array = [];

        if ($this->canUseCanonicalUrl()) {
            $array[] = Meta::property('og:url', $this->getCanonicalUrl());
        }

        return $array;
    }

    protected function canUseCanonicalUrl(): bool
    {
        return Hyde::uriPath() && isset($this->slug);
    }

    protected function getCanonicalUrl(): string
    {
        return Hyde::uriPath(Hyde::pageLink($this->slug . '.html'));
    }
}
