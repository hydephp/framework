<?php

declare(strict_types=1);

namespace Hyde\Framework\Features\Navigation;

use BadMethodCallException;
use function config;
use Hyde\Pages\DocumentationPage;
use Hyde\Pages\MarkdownPost;
use function in_array;

/**
 * @see \Hyde\Framework\Testing\Feature\NavigationMenuTest
 */
class NavigationMenu extends BaseNavigationMenu
{
    public function generate(): static
    {
        parent::generate();

        if ($this->dropdownsEnabled()) {
            $this->putGroupedItemsInDropdowns();
        }

        return $this;
    }

    protected function putGroupedItemsInDropdowns(): void
    {
        $dropdowns = [];

        /** @var \Hyde\Framework\Features\Navigation\NavItem $item */
        foreach ($this->items as $item) {
            if ($this->canBeInDropdown($item)) {
                // Buffer the item in the dropdowns array
                $dropdowns[$item->getGroup()][] = $item;

                // Remove the item from the main items collection
                $this->items->forget($item->route->getRouteKey());
            }
        }

        foreach ($dropdowns as $group => $items) {
            // Create a new dropdown item containing the buffered items
            $this->items->put("dropdown.$group", new DropdownNavItem($group, $items));
        }
    }

    public function hasDropdowns(): bool
    {
        if (! $this->dropdownsEnabled()) {
            return false;
        }

        return count($this->getDropdowns()) >= 1;
    }

    /** @return array<string, DropdownNavItem> */
    public function getDropdowns(): array
    {
        if (! $this->dropdownsEnabled()) {
            throw new BadMethodCallException('Dropdowns are not enabled. Enable it by setting `hyde.navigation.subdirectories` to `dropdown`.');
        }

        return $this->items->filter(function (NavItem $item): bool {
            return $item instanceof DropdownNavItem;
        })->all();
    }

    protected static function canBeInDropdown(NavItem $item): bool
    {
        return ($item->getGroup() !== null) && ! in_array($item->route->getPageClass(), [DocumentationPage::class, MarkdownPost::class]);
    }

    protected static function dropdownsEnabled(): bool
    {
        return config('hyde.navigation.subdirectories', 'hidden') === 'dropdown';
    }

    protected static function shouldItemBeHidden(NavItem $item): bool
    {
        return parent::shouldItemBeHidden($item) ||
            $item->getRoute()?->getPage() instanceof DocumentationPage && ! $item->getRoute()->is('docs/index');
    }
}
