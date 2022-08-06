<?php

namespace Hyde\Framework\Actions;

use Hyde\Framework\Contracts\AbstractMarkdownPage;
use Hyde\Framework\Contracts\AbstractPage;
use Hyde\Framework\Models\Pages\BladePage;
use Hyde\Framework\Models\Pages\DocumentationPage;
use Illuminate\Support\Str;

/**
 * Dynamically constructs data for a page model.
 *
 * @see \Hyde\Framework\Testing\Feature\PageModelConstructorTest
 */
class PageModelConstructor
{
    /**
     * @var AbstractPage|AbstractMarkdownPage|BladePage
     */
    protected AbstractPage|AbstractMarkdownPage|BladePage $page;

    public static function run(AbstractPage $page): AbstractPage
    {
        return (new static($page))->get();
    }

    protected function __construct(AbstractPage $page)
    {
        $this->page = $page;
        $this->constructDynamicData();
    }

    protected function constructDynamicData(): void
    {
        // @deprecated v0.58.x-beta (will be added to docpage schema)
        if ($this->page instanceof DocumentationPage) {
            $this->page->category = static::getDocumentationPageCategory();
        }
    }

    protected function get(): AbstractPage
    {
        return $this->page;
    }

    protected function getDocumentationPageCategory(): ?string
    {
        // If the documentation page is in a subdirectory,
        // then we can use that as the category name.
        // Otherwise, we look in the front matter.

        return str_contains($this->page->identifier, '/')
            ? Str::before($this->page->identifier, '/')
            : $this->page->matter('category');
    }
}
