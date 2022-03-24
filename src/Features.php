<?php

namespace Hyde\Framework;

/**
 * Allows features to be enabled and disabled in a simple object oriented manner.
 *
 * Based entirely on Laravel Jetstream (License MIT)
 * @see https://jetstream.laravel.com/
 */
class Features
{
    /**
     * Determine if the given feature is enabled.
     *
     * @param  string  $feature
     * @return bool
     */
    public static function enabled(string $feature)
    {
        return in_array($feature, config('hyde.features', []));
    }


    /**
     * Determine if the site has blog posts enabled.
     *
     * @return bool
     */
    public static function hasBlogPosts()
    {
        return static::enabled(static::blogPosts());
    }

    /**
     * Determine if the site has custom Blade pages enabled.
     *
     * @return bool
     */
    public static function hasBladePages()
    {
        return static::enabled(static::bladePages());
    }

    /**
     * Determine if the site has custom Markdown pages enabled.
     *
     * @return bool
     */
    public static function hasMarkdownPages()
    {
        return static::enabled(static::markdownPages());
    }

    /**
     * Determine if the site has Laradocgen enabled.
     *
     * @return bool
     */
    public static function hasDocumentationPages()
    {
        return static::enabled(static::documentationPages());
    }

    
    /**
     * Determine if the site has Torchlight enabled.
     *
     * Torchlight is an API for Syntax Highlighting. By default, it is enabled
     * automatically when an API token is set in the .env file.
     *
     * It is disabled when running tests.
     *
     * @param bool $bypassAutomaticCheck if set to true the function will not check if a token is set.
     * @return bool
     */
    public static function hasTorchlight(bool $bypassAutomaticCheck = false)
    {
        if ($bypassAutomaticCheck) {
            return static::enabled(static::torchlight());
        }
        return static::enabled(static::torchlight())
            && (config('torchlight.token') !== null)
            && (app('env') !== 'testing');
    }


    /**
     * Enable the blog post feature.
     *
     * @return string
     */
    public static function blogPosts()
    {
        return 'blog-posts';
    }

    /**
     * Enable the Blade page feature.
     *
     * @return string
     */
    public static function bladePages()
    {
        return 'blade-pages';
    }

    /**
     * Enable the Markdown page feature.
     *
     * @return string
     */
    public static function markdownPages()
    {
        return 'markdown-pages';
    }

    /**
     * Enable the documentation page feature.
     *
     * @return string
     */
    public static function documentationPages()
    {
        return 'documentation-pages';
    }

    /**
     * Enable the Torchlight integration.
     *
     * @return string
     */
    public static function torchlight()
    {
        return 'torchlight';
    }
}
