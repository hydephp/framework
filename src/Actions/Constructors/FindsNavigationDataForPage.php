<?php

namespace Hyde\Framework\Actions\Constructors;

use Hyde\Framework\Concerns\AbstractPage;
use Hyde\Framework\Models\Pages\DocumentationPage;
use Hyde\Framework\Models\Pages\MarkdownPost;
use JetBrains\PhpStorm\ArrayShape;

/**
 * Finds the appropriate navigation data for a page.
 *
 * @see \Hyde\Framework\Testing\Feature\AbstractPageTest
 */
class FindsNavigationDataForPage
{
    #[ArrayShape(['title' => 'string', 'hidden' => 'bool', 'priority' => 'int'])]
    public static function run(AbstractPage $page): array
    {
        return (new static($page))->getData();
    }

    final protected function __construct(protected AbstractPage $page)
    {
    }

    #[ArrayShape(['title' => 'string', 'hidden' => 'bool', 'priority' => 'int'])]
    protected function getData(): array
    {
        return [
            'title' => $this->getNavigationMenuTitle(),
            'hidden' => ! $this->getNavigationMenuVisible(),
            'priority' => $this->getNavigationMenuPriority(),
        ];
    }

    /**
     * Note that this also affects the documentation sidebar titles.
     */
    protected function getNavigationMenuTitle(): string
    {
        if ($this->page->matter('navigation.title') !== null) {
            return $this->page->matter('navigation.title');
        }

        if ($this->page->identifier === 'index') {
            if ($this->page instanceof DocumentationPage) {
                return config('hyde.navigation.labels.docs', 'Docs');
            }

            return config('hyde.navigation.labels.home', 'Home');
        }

        return $this->page->matter('title') ?? $this->page->title;
    }

    protected function getNavigationMenuVisible(): bool
    {
        if ($this->page instanceof MarkdownPost) {
            return false;
        }

        if ($this->page instanceof DocumentationPage) {
            return $this->page->identifier === 'index' && ! in_array($this->page->routeKey, config('hyde.navigation.exclude', []));
        }

        if ($this->page->matter('navigation.hidden', false)) {
            return false;
        }

        if (in_array($this->page->identifier, config('hyde.navigation.exclude', ['404']))) {
            return false;
        }

        return true;
    }

    protected function getNavigationMenuPriority(): int
    {
        if ($this->page->matter('navigation.priority') !== null) {
            return $this->page->matter('navigation.priority');
        }

        if (array_key_exists($this->page->routeKey, config('hyde.navigation.order', []))) {
            return (int) config('hyde.navigation.order.'.$this->page->routeKey);
        }

        return 999;
    }
}
