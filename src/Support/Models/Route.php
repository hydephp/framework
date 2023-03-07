<?php

declare(strict_types=1);

namespace Hyde\Support\Models;

use Hyde\Hyde;
use Hyde\Pages\Concerns\HydePage;
use Hyde\Support\Concerns\Serializable;
use Hyde\Support\Contracts\SerializableContract;
use Stringable;

/**
 * The Route class bridges the gaps between Hyde pages and their respective compiled static webpages
 * by providing helper methods and information allowing you to easily access and interact with the
 * various paths associated with a page, both source and compiled file paths as well as the URL.
 *
 * If you visualize a web of this class's properties, you should be able to see how this
 * class links them all together, and what powerful information you can gain from it.
 *
 * @see \Hyde\Framework\Testing\Unit\RouteTest
 */
class Route implements Stringable, SerializableContract
{
    use Serializable;

    protected HydePage $page;

    public function __construct(HydePage $page)
    {
        $this->page = $page;
    }

    /**
     * Cast a route object into a string that can be used in a href attribute.
     */
    public function __toString(): string
    {
        return $this->getLink();
    }

    /**
     * Generate a link to the route destination, relative to the current route, and supports pretty URLs.
     */
    public function getLink(): string
    {
        return Hyde::relativeLink($this->page->getLink());
    }

    public function getPage(): HydePage
    {
        return $this->page;
    }

    /** @return class-string<HydePage> */
    public function getPageClass(): string
    {
        return $this->page::class;
    }

    public function getPageIdentifier(): string
    {
        return $this->page->getIdentifier();
    }

    public function getRouteKey(): string
    {
        return $this->page->getRouteKey();
    }

    public function getSourcePath(): string
    {
        return $this->page->getSourcePath();
    }

    public function getOutputPath(): string
    {
        return $this->page->getOutputPath();
    }

    /**
     * Determine if the route instance matches another route or route key.
     */
    public function is(Route|RouteKey|string $route): bool
    {
        if ($route instanceof Route) {
            return $this->getRouteKey() === $route->getRouteKey();
        }

        return $this->getRouteKey() === (string) $route;
    }

    /**
     * @return array{routeKey: string, sourcePath: string, outputPath: string, page: array{class: string, identifier: string}}
     */
    public function toArray(): array
    {
        return [
            'routeKey' => $this->getRouteKey(),
            'sourcePath' => $this->getSourcePath(),
            'outputPath' => $this->getOutputPath(),
            'page' => [
                'class' => $this->getPageClass(),
                'identifier' => $this->getPageIdentifier(),
            ],
        ];
    }
}
