<?php

declare(strict_types=1);

namespace Hyde\Framework\Services;

use function class_exists;
use function config;
use function glob;
use Hyde\Foundation\Facades\FileCollection;
use Hyde\Framework\Exceptions\UnsupportedPageTypeException;
use Hyde\Hyde;
use Hyde\Pages\BladePage;
use Hyde\Pages\Concerns\HydePage;
use Hyde\Pages\DocumentationPage;
use Hyde\Pages\MarkdownPage;
use Hyde\Pages\MarkdownPost;
use Hyde\Support\Filesystem\SourceFile;
use Illuminate\Support\Str;
use function implode;
use function is_array;
use function is_subclass_of;
use function sprintf;
use function str_replace;
use function unslash;

/**
 * The core service that powers all HydePHP file auto-discovery.
 *
 * Contains service methods to return helpful collections of arrays and lists,
 * and provides helper methods for source file auto-discovery used in the site
 * building process to determine where files are located and how to parse them.
 *
 * @see \Hyde\Framework\Testing\Feature\DiscoveryServiceTest
 */
class DiscoveryService
{
    final public const DEFAULT_MEDIA_EXTENSIONS = ['png', 'svg', 'jpg', 'jpeg', 'gif', 'ico', 'css', 'js'];

    public static function getBladePageFiles(): array
    {
        return static::getSourceFileListForModel(BladePage::class);
    }

    public static function getMarkdownPageFiles(): array
    {
        return static::getSourceFileListForModel(MarkdownPage::class);
    }

    public static function getMarkdownPostFiles(): array
    {
        return static::getSourceFileListForModel(MarkdownPost::class);
    }

    public static function getDocumentationPageFiles(): array
    {
        return static::getSourceFileListForModel(DocumentationPage::class);
    }

    /**
     * Supply a model::class constant and get a list of all the existing source file base names.
     *
     * @param  class-string<\Hyde\Pages\Concerns\HydePage>  $model
     * @return array<string>
     *
     * @throws \Hyde\Framework\Exceptions\UnsupportedPageTypeException
     *
     * @example Usage: DiscoveryService::getSourceFileListForModel(BladePage::class)
     * @example Returns: ['index', 'about', 'contact']
     */
    public static function getSourceFileListForModel(string $model): array
    {
        if (! class_exists($model) || ! is_subclass_of($model, HydePage::class)) {
            throw new UnsupportedPageTypeException($model);
        }

        return FileCollection::getSourceFiles($model)->flatten()->map(function (SourceFile $file) use ($model): string {
            return static::pathToIdentifier($model, $file->withoutDirectoryPrefix());
        })->toArray();
    }

    /** @param class-string<\Hyde\Pages\Concerns\HydePage> $model */
    public static function getModelFileExtension(string $model): string
    {
        return $model::fileExtension();
    }

    /** @param class-string<\Hyde\Pages\Concerns\HydePage> $model */
    public static function getModelSourceDirectory(string $model): string
    {
        return $model::sourceDirectory();
    }

    /**
     * Format a filename to an identifier for a given model. Unlike the basename function, any nested paths
     * within the source directory are retained in order to satisfy the page identifier definition.
     *
     * @param  class-string<\Hyde\Pages\Concerns\HydePage>  $model
     * @param  string  $filepath  Example: index.blade.php
     * @return string Example: index
     */
    public static function pathToIdentifier(string $model, string $filepath): string
    {
        return unslash(Str::between(Hyde::pathToRelative($filepath),
            $model::$sourceDirectory.'/',
            $model::$fileExtension)
        );
    }

    /**
     * Get all the Media asset file paths.
     * Returns a full file path, unlike the other get*List methods.
     *
     * @return array<string> An array of absolute file paths.
     */
    public static function getMediaAssetFiles(): array
    {
        return glob(Hyde::path(static::getMediaGlobPattern()), GLOB_BRACE) ?: [];
    }

    protected static function getMediaGlobPattern(): string
    {
        return sprintf('_media/{*,**/*,**/*/*}.{%s}', static::parseConfiguredMediaExtensions(
            config('hyde.media_extensions', self::DEFAULT_MEDIA_EXTENSIONS)
        ));
    }

    protected static function parseConfiguredMediaExtensions(string|array|null $extensions): string
    {
        return is_array($extensions)
            ? implode(',', $extensions)
            : static::removeSpaces((string) $extensions);
    }

    protected static function removeSpaces(string $string): string
    {
        return str_replace(' ', '', $string);
    }
}
