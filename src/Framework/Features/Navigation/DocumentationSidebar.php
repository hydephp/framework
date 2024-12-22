<?php

declare(strict_types=1);

namespace Hyde\Framework\Features\Navigation;

use Hyde\Hyde;
use Hyde\Facades\Config;
use Hyde\Foundation\Facades\Routes;
use Hyde\Pages\DocumentationPage;
use Hyde\Support\Facades\Render;
use Hyde\Support\Models\Route;
use Illuminate\Support\Collection;

use function collect;

class DocumentationSidebar extends BaseNavigationMenu
{
    protected function generate(): void
    {
        Routes::getRoutes(DocumentationPage::class)->each(function (Route $route): void {
            if ($this->canAddRoute($route)) {
                $this->items->put($route->getRouteKey(), NavItem::fromRoute($route));
            }
        });

        // If there are no pages other than the index page, we add it to the sidebar so that it's not empty
        if ($this->items->count() === 0 && DocumentationPage::home() !== null) {
            $this->items->push(NavItem::fromRoute(DocumentationPage::home(), group: 'other'));
        }
    }

    public function hasGroups(): bool
    {
        return (count($this->getGroups()) >= 1) && ($this->getGroups() !== ['other']);
    }

    /** @return array<string> */
    public function getGroups(): array
    {
        return $this->items->map(function (NavItem $item): string {
            return $item->getGroup();
        })->unique()->toArray();
    }

    /** @return Collection<\Hyde\Framework\Features\Navigation\NavItem> */
    public function getItemsInGroup(?string $group): Collection
    {
        return $this->items->filter(function (NavItem $item) use ($group): bool {
            return ($item->getGroup() === $group) || ($item->getGroup() === Hyde::makeSlug($group));
        })->sortBy('navigation.priority')->values();
    }

    public function isGroupActive(string $group): bool
    {
        $normalized = Hyde::makeSlug(Render::getPage()->navigationMenuGroup() ?? 'other');

        return ($normalized === $group) || ($this->isPageIndexPage() && $this->shouldIndexPageBeActive($group));
    }

    public function makeGroupTitle(string $group): string
    {
        return Config::getNullableString("docs.sidebar_group_labels.$group") ?? Hyde::makeTitle($group);
    }

    protected function canAddRoute(Route $route): bool
    {
        return parent::canAddRoute($route) && ! $route->is(DocumentationPage::homeRouteName());
    }

    private function isPageIndexPage(): bool
    {
        return Render::getPage()->getRoute()->is(DocumentationPage::homeRouteName());
    }

    private function shouldIndexPageBeActive(string $group): bool
    {
        return Render::getPage()->navigationMenuGroup() === 'other' && $group === collect($this->getGroups())->first();
    }
}
