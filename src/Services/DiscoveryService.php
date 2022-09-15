<?php

namespace Hyde\Framework\Services;

use Hyde\Framework\Concerns\HydePage;
use Hyde\Framework\Exceptions\UnsupportedPageTypeException;
use Hyde\Framework\Hyde;
use Hyde\Framework\Models\Pages\BladePage;
use Hyde\Framework\Models\Pages\DocumentationPage;
use Hyde\Framework\Models\Pages\MarkdownPage;
use Hyde\Framework\Models\Pages\MarkdownPost;
use Hyde\Framework\Models\Support\File;

/**
 * The core service that powers all HydePHP file auto-discovery.
 *
 * Contains service methods to return helpful collections of arrays and lists,
 * and provides helper methods for source file auto-discovery used in the site
 * building process to determine where files are located and how to parse them.
 *
 * The CollectionService was in v0.53.0 merged into this class.
 *
 * @see \Hyde\Framework\Testing\Feature\DiscoveryServiceTest
 */
class DiscoveryService
{
    /**
     * Supply a model::class constant and get a list of all the existing source file base names.
     *
     * @param  string<\Hyde\Framework\Concerns\HydePage>  $model
     * @return array
     *
     * @throws \Hyde\Framework\Exceptions\UnsupportedPageTypeException
     *
     * @example DiscoveryService::getSourceFileListForModel(BladePage::class)
     */
    public static function getSourceFileListForModel(string $model): array
    {
        if (! class_exists($model) || ! is_subclass_of($model, HydePage::class)) {
            throw new UnsupportedPageTypeException($model);
        }

        $files = [];
        Hyde::files()->getSourceFiles($model)->each(function (File $file) use (&$files, $model) {
            $files[] = self::formatSlugForModel($model, $file->withoutDirectoryPrefix());
        });

        return $files;
    }

    public static function getModelFileExtension(string $model): string
    {
        /** @var \Hyde\Framework\Concerns\HydePage $model */
        return $model::fileExtension();
    }

    public static function getModelSourceDirectory(string $model): string
    {
        /** @var \Hyde\Framework\Concerns\HydePage $model */
        return $model::sourceDirectory();
    }

    public static function getBladePageFiles(): array
    {
        return self::getSourceFileListForModel(BladePage::class);
    }

    public static function getMarkdownPageFiles(): array
    {
        return self::getSourceFileListForModel(MarkdownPage::class);
    }

    public static function getMarkdownPostFiles(): array
    {
        return self::getSourceFileListForModel(MarkdownPost::class);
    }

    public static function getDocumentationPageFiles(): array
    {
        return self::getSourceFileListForModel(DocumentationPage::class);
    }

    /**
     * Get all the Media asset file paths.
     * Returns a full file path, unlike the other get*List methods.
     */
    public static function getMediaAssetFiles(): array
    {
        return glob(Hyde::path(static::getMediaGlobPattern()), GLOB_BRACE) ?: [];
    }

    /**
     * Create a filepath that can be opened in the browser from a terminal.
     *
     * @param  string<\Hyde\Framework\Concerns\HydePage>  $filepath
     * @return string
     */
    public static function createClickableFilepath(string $filepath): string
    {
        if (realpath($filepath) === false) {
            return $filepath;
        }

        return 'file://'.str_replace(
            '\\',
            '/',
            realpath($filepath)
        );
    }

    public static function formatSlugForModel(string $model, string $filepath): string
    {
        /** @var HydePage $model */
        $slug = str_replace(Hyde::path($model::$sourceDirectory), '', $filepath);

        if (str_ends_with($slug, $model::$fileExtension)) {
            $slug = substr($slug, 0, -strlen($model::$fileExtension));
        }

        return unslash($slug);
    }

    protected static function getMediaGlobPattern(): string
    {
        return sprintf('_media/*.{%s}', str_replace(' ', '',
            config('hyde.media_extensions', 'png,svg,jpg,jpeg,gif,ico,css,js')
        ));
    }
}
