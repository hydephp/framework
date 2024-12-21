<?php

declare(strict_types=1);

namespace Hyde\Foundation\Kernel;

use Hyde\Facades\Config;
use Hyde\Support\Models\Route;
use Hyde\Foundation\HydeKernel;
use JetBrains\PhpStorm\Deprecated;
use Hyde\Framework\Exceptions\BaseUrlNotSetException;
use Hyde\Framework\Exceptions\FileNotFoundException;
use Illuminate\Support\Str;

use function str_ends_with;
use function str_starts_with;
use function substr_count;
use function file_exists;
use function str_replace;
use function str_repeat;
use function substr;
use function blank;
use function rtrim;
use function trim;

/**
 * Contains helpers and logic for resolving web paths for compiled files.
 *
 * It's bound to the HydeKernel instance, and is an integral part of the framework.
 */
class Hyperlinks
{
    protected HydeKernel $kernel;

    public function __construct(HydeKernel $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * Format a web link to an HTML file, allowing for pretty URLs, if enabled.
     *
     * @see \Hyde\Framework\Testing\Unit\Foundation\HyperlinkFormatHtmlPathTest
     */
    public function formatLink(string $destination): string
    {
        if (Config::getBool('hyde.pretty_urls', false) === true) {
            if (str_ends_with($destination, '.html')) {
                if ($destination === 'index.html') {
                    return '/';
                }

                if (str_ends_with($destination, 'index.html')) {
                    return substr($destination, 0, -10);
                }

                return substr($destination, 0, -5);
            }
        }

        return $destination;
    }

    /**
     * Inject the proper number of `../` before the links in Blade templates.
     *
     * @param  string  $destination  relative to output directory on compiled site
     *
     * @see \Hyde\Framework\Testing\Unit\Foundation\HyperlinkFileHelperRelativeLinkTest
     */
    public function relativeLink(string $destination): string
    {
        if (str_starts_with($destination, '../')) {
            return $destination;
        }

        $nestCount = substr_count($this->kernel->currentRouteKey() ?? '', '/');
        $route = '';
        if ($nestCount > 0) {
            $route .= str_repeat('../', $nestCount);
        }
        $route .= $this->formatLink($destination);

        if (Config::getBool('hyde.pretty_urls', false) === true && $route === '/') {
            return './';
        }

        return str_replace('//', '/', $route);
    }

    /**
     * Gets a relative web link to the given file stored in the _site/media folder.
     *
     * An exception will be thrown if the file does not exist in the _media directory,
     * and the second argument is set to true.
     *
     * @deprecated This method will be removed in v2.0. Please use `asset()` instead.
     */
    #[Deprecated(reason: 'Use `asset` method instead.', replacement: '%class%->asset(%parameter0%)')]
    public function mediaLink(string $destination, bool $validate = false): string
    {
        if ($validate && ! file_exists($sourcePath = "{$this->kernel->getMediaDirectory()}/$destination")) {
            throw new FileNotFoundException($sourcePath);
        }

        return $this->relativeLink("{$this->kernel->getMediaOutputDirectory()}/$destination");
    }

    /**
     * Gets a relative web link to the given image stored in the _site/media folder.
     * If the image is remote (starts with http) it will be returned as is.
     *
     * If true is passed as the second argument, and a base URL is set,
     * the image will be returned with a qualified absolute URL.
     */
    public function asset(string $name, bool $preferQualifiedUrl = false): string
    {
        if (static::isRemote($name)) {
            return $name;
        }

        $name = Str::start($name, "{$this->kernel->getMediaOutputDirectory()}/");

        if ($preferQualifiedUrl && $this->hasSiteUrl()) {
            return $this->url($name);
        }

        return $this->relativeLink($name);
    }

    /**
     * Check if a site base URL has been set in config (or .env).
     *
     * The default value is `http://localhost`, which is not considered a valid site URL.
     */
    public function hasSiteUrl(): bool
    {
        $value = Config::getNullableString('hyde.url');

        return ! blank($value) && $value !== 'http://localhost';
    }

    /**
     * Return a qualified URL to the supplied path if a base URL is set.
     *
     * @param  string  $path  An optional relative path suffix. Omit to return the base URL.
     *
     * @throws BaseUrlNotSetException If no site URL is set and no path is provided.
     */
    public function url(string $path = ''): string
    {
        $path = $this->formatLink(trim($path, '/'));

        if (static::isRemote($path)) {
            return $path;
        }

        if ($this->hasSiteUrl()) {
            return rtrim(rtrim(Config::getString('hyde.url'), '/')."/$path", '/');
        }

        // Since v1.7.0, we return the relative path even if the base URL is not set,
        // as this is more likely to be the desired behavior the user's expecting.
        if (! blank($path)) {
            return $path;
        }

        // User is trying to get the base URL, but it's not set
        // This exception is deprecated and will be removed in v2.0.0, and we will throw a BadMethodCallException instead.
        throw new BaseUrlNotSetException();
    }

    /**
     * Get a route instance by its key from the kernel's route collection.
     */
    public function route(string $key): ?Route
    {
        return $this->kernel->routes()->get($key);
    }

    /**
     * Determine if the given URL is a remote link.
     */
    public static function isRemote(string $url): bool
    {
        return str_starts_with($url, 'http') || str_starts_with($url, '//');
    }
}
