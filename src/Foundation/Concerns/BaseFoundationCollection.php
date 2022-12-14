<?php

declare(strict_types=1);

namespace Hyde\Foundation\Concerns;

use Hyde\Foundation\HydeKernel;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;

/**
 * Base class for the kernel auto-discovery collections.
 *
 * @see \Hyde\Foundation\FileCollection
 * @see \Hyde\Foundation\PageCollection
 * @see \Hyde\Foundation\RouteCollection
 * @see \Hyde\Framework\Testing\Unit\BaseFoundationCollectionTest
 */
abstract class BaseFoundationCollection extends Collection
{
    protected HydeKernel $kernel;

    abstract protected function runDiscovery(): self;

    public static function boot(HydeKernel $kernel): static
    {
        return (new static())->setKernel($kernel)->runDiscovery();
    }

    protected function __construct(array|Arrayable|null $items = [])
    {
        parent::__construct($items);
    }

    /** @return $this */
    protected function setKernel(HydeKernel $kernel): static
    {
        $this->kernel = $kernel;

        return $this;
    }

    /** @return $this */
    public function getInstance(): static
    {
        return $this;
    }
}
