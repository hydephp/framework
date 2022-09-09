<?php

namespace Hyde\Framework;

use Hyde\Framework\Contracts\RouteContract;
use Hyde\Framework\Foundation\FileCollection;
use Hyde\Framework\Foundation\PageCollection;
use Hyde\Framework\Foundation\RouteCollection;
use Hyde\Framework\Helpers\Features;
use Illuminate\Support\Facades\Facade;

/**
 * General facade for Hyde services.
 *
 * @see \Hyde\Framework\HydeKernel
 *
 * @author  Caen De Silva <caen@desilva.se>
 * @copyright 2022 Caen De Silva
 * @license MIT License
 *
 * @method static string path(string $path = '')
 * @method static string vendorPath(string $path = '')
 * @method static string pathToRelative(string $path)
 * @method static string getModelSourcePath(string $model, string $path = '')
 * @method static string getBladePagePath(string $path = '')
 * @method static string getMarkdownPagePath(string $path = '')
 * @method static string getMarkdownPostPath(string $path = '')
 * @method static string getDocumentationPagePath(string $path = '')
 * @method static string sitePath(string $path = '')
 * @method static string formatHtmlPath(string $destination)
 * @method static string relativeLink(string $destination)
 * @method static string image(string $name, bool $preferQualifiedUrl = false)
 * @method static string url(string $path = '', null|string $default = null)
 * @method static string makeTitle(string $slug)
 * @method static string currentPage()
 * @method static string getBasePath()
 * @method static Features features()
 * @method static FileCollection files()
 * @method static PageCollection pages()
 * @method static RouteCollection routes()
 * @method static RouteContract|null currentRoute()
 * @method static HydeKernel getInstance()
 * @method static bool hasFeature(string $feature)
 * @method static bool hasSiteUrl()
 * @method static bool copy(string $from, string $to)
 * @method static bool touch(array|string $path)
 * @method static bool unlink(array|string $path)
 * @method static void setInstance(HydeKernel $instance)
 * @method static void setBasePath(string $basePath)
 * @method static array toArray()
 * @method static void boot()
 */
class Hyde extends Facade
{
    public static function version(): string
    {
        return HydeKernel::version();
    }

    public static function getFacadeRoot()
    {
        return HydeKernel::getInstance();
    }
}
