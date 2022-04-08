<?php

namespace Hyde\Framework;

use Composer\InstalledVersions;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * General interface for Hyde services.
 */
class Hyde
{
    /**
     * Return the Composer Package Version.
     *
     * @return string
     */
    public static function version(): string
    {
        return InstalledVersions::getPrettyVersion('hyde/framework') ?: 'unreleased';
    }

    /**
     * Get the subdirectory documentation files are stored in.
     *
     * @return string
     */
    public static function docsDirectory(): string
    {
        return trim(config('hyde.docsDirectory', 'docs'), '/\\');
    }

    /**
     * Get the path to the frontpage for the documentation.
     *
     * @return string|false returns false if no frontpage is found
     */
    public static function docsIndexPath(): string|false
    {
        if (file_exists(Hyde::path('_docs/index.md'))) {
            return Hyde::docsDirectory().'/index.html';
        }

        if (file_exists(Hyde::path('_docs/readme.md'))) {
            return Hyde::docsDirectory().'/readme.html';
        }

        return false;
    }

    /**
     * Get an absolute path from a supplied relative path.
     *
     * The function returns the fully qualified path to your site's root directory.
     *
     * You may also use the function to generate a fully qualified path to a given file
     * relative to the project root directory when supplying the path argument.
     *
     * @param  string  $path
     * @return string
     */
    public static function path(string $path = ''): string
    {
        if (empty($path)) {
            return getcwd();
        }

        $path = trim($path, '/\\');

        return getcwd().DIRECTORY_SEPARATOR.$path;
    }

    /**
     * Inject the proper number of `../` before the links.
     *
     * @param  string  $destination  the route to format
     * @param  string  $current  the current route
     * @return string
     */
    public static function relativePath(string $destination, string $current = ''): string
    {
        $nestCount = substr_count($current, '/');
        $route = '';
        if ($nestCount > 0) {
            $route .= str_repeat('../', $nestCount);
        }
        $route .= $destination;

        return $route;
    }

    /**
     * Return a qualified URI path, if SITE_URL is set in .env, else return false.
     *
     * @param  string|null  $path  optional relative path suffix. Omit to return base url.
     * @return string|false
     */
    public static function uriPath(?string $path = ''): string|false
    {
        if (config('hyde.site_url', false)) {
            return rtrim(config('hyde.site_url'), '/').'/'.(trim($path, '/') ?? '');
        }

        return false;
    }

    /**
     * Wrapper for the copy function, but allows choosing if files may be overwritten.
     *
     * @param  string  $from  The source file path.
     * @param  string  $to  The destination file path.
     * @param  bool  $force  If true, existing files will be overwritten.
     * @return bool|int Returns true|false on copy() success|failure, or an error code on failure
     */
    public static function copy(string $from, string $to, bool $force = false): bool|int
    {
        if (! file_exists($from)) {
            return 404;
        }

        if (file_exists($to) && ! $force) {
            return 409;
        }

        return copy($from, $to);
    }

    /**
     * Create a title from a kebab-case slug.
     *
     * @param string $slug
     * @return string $title
     */
    public static function titleFromSlug(string $slug): string
    {
        return Str::title(str_replace('-', ' ', ($slug)));
    }

    /**
     * Get a Laravel Collection of all Posts as MarkdownPost objects.
     *
     * Serves as a static shorthand for \Hyde\Framework\Models\MarkdownPost::getCollection()
     *
     * @return \Illuminate\Support\Collection
     *
     * @throws \Exception
     */
    public static function getLatestPosts(): Collection
    {
        $collection = new Collection();

        foreach (glob(Hyde::path('_posts/*.md')) as $filepath) {
            $collection->push((new MarkdownPostParser(basename($filepath, '.md')))->get());
        }

        return $collection->sortByDesc('matter.date');
    }
}
