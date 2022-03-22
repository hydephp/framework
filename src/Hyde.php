<?php

namespace Hyde\Framework;

/**
 * General interface for Hyde services
 */
class Hyde
{
    /**
     * Is Torchlight enabled?
     *
     * @deprecated v0.4.1 will be moved into the Features class
     * 
     * Torchlight is an API for Syntax Highlighting. By default, it is enabled
     * automatically when an API token is set in the .env file.
     * @return bool
     */
    public static function hasTorchlight(): bool
    {
        return (config('torchlight.token') !== null);
    }

    /**
     * Return the path where the Blade views are located
     * 
     * @deprecated v0.4.1 as it is not needed
     * 
     * @return string
     */
    public static function viewPath()
    {
        return resource_path('views') ;
    }

    /**
     * Get an absolute path from a supplied relative path.
     *
     * The function returns the fully qualified path to your site's root directory.
     *
     * You may also use the function to generate a fully qualified path to a given file
     * relative to the project root directory when supplying the path argument.
     *
     * @param string $path
     * @return string
     */
    public static function path(string $path = ''): string
    {
        if (empty($path)) {
            return getcwd();
        }

        $path = trim($path, '/\\');

        return getcwd() . DIRECTORY_SEPARATOR . $path;
    }


    /**
     * Inject the proper number of `../` before the links
     * 
     * @param string $destination the route to format
     * @param string $current the current route
     * @return string
     */
    public static function relativePath(string $destination, string $current = ""): string
    {
        $nestCount = substr_count($current, '/');
        $route = '';
        if ($nestCount > 0) {
            $route .= str_repeat('../', $nestCount);
        }
        $route .= $destination ;
        return $route;
    }
}
