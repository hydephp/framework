<?php

declare(strict_types=1);

namespace Hyde\Support\Internal;

use Hyde\Hyde;
use Hyde\Pages\InMemoryPage;
use Hyde\Support\Models\Route;
use Illuminate\Contracts\Support\Arrayable;

use function class_basename;
use function str_starts_with;

/**
 * @internal This class is internal and should not be depended on outside the HydePHP framework code.
 */
class RouteListItem implements Arrayable
{
    protected Route $route;

    public function __construct(Route $route)
    {
        $this->route = $route;
    }

    public function toArray(): array
    {
        return [
            'page_type' => $this->stylePageType($this->route->getPageClass()),
            'source_file' => $this->styleSourcePath($this->route->getSourcePath()),
            'output_file' => $this->styleOutputPath($this->route->getOutputPath()),
            'route_key' => $this->styleRouteKey($this->route->getRouteKey()),
        ];
    }

    protected function stylePageType(string $class): string
    {
        return str_starts_with($class, 'Hyde') ? class_basename($class) : $class;
    }

    protected function styleSourcePath(string $path): string
    {
        return $this->isPageDiscoverable() ? $path : 'none';
    }

    protected function styleOutputPath(string $path): string
    {
        return Hyde::getOutputDirectory()."/$path";
    }

    protected function styleRouteKey(string $key): string
    {
        return $key;
    }

    protected function isPageDiscoverable(): bool
    {
        return $this->route->getSourcePath() && ! $this->route->getPage() instanceof InMemoryPage;
    }
}
