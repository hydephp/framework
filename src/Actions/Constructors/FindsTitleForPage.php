<?php

namespace Hyde\Framework\Actions\Constructors;

use Hyde\Framework\Concerns\AbstractMarkdownPage;
use Hyde\Framework\Concerns\HydePage;
use Hyde\Framework\Hyde;

/**
 * @see \Hyde\Framework\Testing\Feature\PageModelConstructorsTest
 *
 * @internal
 */
final class FindsTitleForPage
{
    public static function run(HydePage $page): string
    {
        return trim((new self($page))->findTitleForPage());
    }

    protected function __construct(protected HydePage $page)
    {
    }

    protected function findTitleForPage(): string
    {
        return $this->page->matter('title')
                ?? $this->findTitleFromMarkdownHeadings()
                ?? Hyde::makeTitle($this->page->identifier);
    }

    protected function findTitleFromMarkdownHeadings(): ?string
    {
        if ($this->page instanceof AbstractMarkdownPage) {
            foreach ($this->page->markdown()->toArray() as $line) {
                if (str_starts_with($line, '# ')) {
                    return trim(substr($line, 2), ' ');
                }
            }
        }

        return null;
    }
}
