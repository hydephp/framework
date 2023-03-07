<?php

declare(strict_types=1);

namespace Hyde\Framework\Factories\Concerns;

use Illuminate\Contracts\Support\Arrayable;

/**
 * Streamlines the data dynamic construction specific to a HydePHP page.
 *
 * Simply pass along the data the class needs to run, then access the parsed data using the toArray() method.
 *
 * Commonly, all data can be set using front matter in the page source file.
 *
 * However, as all front matter is optional in Hyde, if no front matter is set for the given key,
 * the factory may attempt to generate and discover the values based on the page's contents,
 * as well as the project's overall configuration.
 *
 * In other words, this is where the magic happens.
 */
abstract class PageDataFactory implements Arrayable
{
    /**
     * The front matter properties supported by this factory.
     *
     * @var array<string, string|array>
     */
    public const SCHEMA = [];

    /**
     * Get the generated data as an associative array.
     *
     * @return array<string, mixed>
     */
    abstract public function toArray(): array;
}
