<?php

namespace Hyde\Framework\Models;

use Hyde\Framework\Contracts\PageContract;
use Hyde\Framework\Contracts\RouteContract;
use Hyde\Framework\Hyde;
use Illuminate\Support\Str;

/**
 * Abstraction for a navigation menu item.
 *
 * You have a few options to construct a navigation menu item:
 *   1. You can supply a Route directly and explicit properties to the constructor
 *   2. You can use NavItem::fromRoute() to use data from the route
 *   3. You can use NavItem::leadsTo(URI, Title, ?priority) for an external or un-routed link
 */
class NavItem implements \Stringable
{
    public RouteContract $route;
    public string $href;

    public string $title;
    public int $priority;
    public bool $hidden;

    /**
     * Create a new navigation menu item.
     *
     * @param  \Hyde\Framework\Contracts\RouteContract|null  $route
     * @param  string  $title
     * @param  int  $priority
     * @param  bool  $hidden
     */
    public function __construct(?RouteContract $route, string $title, int $priority = 500, bool $hidden = false)
    {
        if ($route !== null) {
            $this->route = $route;
        }

        $this->title = $title;
        $this->priority = $priority;
        $this->hidden = $hidden;
    }

    /**
     * Create a new navigation menu item from a route.
     */
    public static function fromRoute(RouteContract $route): static
    {
        return new self(
            $route,
            $route->getSourceModel()->navigationMenuTitle(),
            $route->getSourceModel()->navigationMenuPriority(),
            ! $route->getSourceModel()->showInNavigation()
        );
    }

    /**
     * Create a new navigation menu item leading to an external URI.
     */
    public static function toLink(string $href, string $title, int $priority = 500): static
    {
        return (new self(null, $title, $priority, false))->setDestination($href);
    }

    /**
     * Create a new navigation menu item leading to a Route model.
     */
    public static function toRoute(RouteContract $route, string $title, int $priority = 500): static
    {
        return new self($route, $title, $priority, false);
    }

    /**
     * Resolve a link to the navigation item.
     */
    public function resolveLink(): string
    {
        return $this->href ?? $this->route->getLink();
    }

    /**
     * Resolve a link to the navigation item.
     */
    public function __toString(): string
    {
        return $this->resolveLink();
    }

    /**
     * Check if the NavItem instance is the current page.
     */
    public function isCurrent(?PageContract $current = null): bool
    {
        if ($current === null) {
            $current = Hyde::currentRoute()->getSourceModel();
        }

        if (! isset($this->route)) {
            return ($current->getRoute()->getRouteKey() === $this->href)
            || ($current->getRoute()->getRouteKey().'.html' === $this->href);
        }

        return $current->getRoute()->getRouteKey() === $this->route->getRouteKey();
    }

    protected function setDestination(string $href): self
    {
        $this->href = $href;

        return $this;
    }

    public function setPriority(int $priority): self
    {
        $this->priority = $priority;

        return $this;
    }

    public function getGroup(): ?string
    {
        return $this->normalizeGroupKey($this->route->getSourceModel()->get('category'));
    }

    protected function normalizeGroupKey(?string $group): ?string
    {
        return empty($group) ? null : Str::slug($group);
    }
}
