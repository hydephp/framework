<?php

declare(strict_types=1);

namespace Hyde\Support\Models;

use Stringable;
use function unslash;

/**
 * Route keys are the core of Hyde's routing system.
 *
 * In short, the route key is the URL path relative to the site webroot, without the file extension.
 *
 * For example, `_pages/index.blade.php` would be compiled to `_site/index.html` and thus has the route key of `index`.
 * As another example, `_posts/welcome.md` would be compiled to `_site/posts/welcome.html` and thus has the route key of `posts/welcome`.
 *
 * Note that if the source page's output directory is changed, the route key will change accordingly.
 * This can potentially cause links to break when changing the output directory for a page class.
 */
final class RouteKey implements Stringable
{
    protected readonly string $key;

    public static function make(string $key): self
    {
        return new self($key);
    }

    public function __construct(string $key)
    {
        $this->key = $key;
    }

    public function __toString(): string
    {
        return $this->key;
    }

    public function get(): string
    {
        return $this->key;
    }

    /** @param class-string<\Hyde\Pages\Concerns\HydePage> $pageClass */
    public static function fromPage(string $pageClass, string $identifier): self
    {
        return new self(unslash("{$pageClass::baseRouteKey()}/$identifier"));
    }
}
