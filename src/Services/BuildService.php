<?php

namespace Hyde\Framework\Services;

use Hyde\Framework\Models\BladePage;
use Hyde\Framework\Models\DocumentationPage;
use Hyde\Framework\Models\MarkdownPage;
use Hyde\Framework\Models\MarkdownPost;

/**
 * Static service helpers for building static pages.
 */
class BuildService
{
    public static function getParserClassForModel(string $model): string
    {
        return $model::$parserClass;
    }

    /**
     * Create and get a constructed instance of a Model's Parser class.
     *
     * @param  string  $model  Class constant of the Model to get the Parser for.
     * @param  string  $slug  The slug of the source file to parse.
     *
     * @example getParserForModel(MarkdownPost::class, 'hello-world')
     *
     * @return object The constructed Parser instance.
     */
    public static function getParserInstanceForModel(string $model, string $slug): object
    {
        return new $model::$parserClass($slug);
    }

    /**
     * Get the file extension for a models source files.
     */
    public static function getFileExtensionForModelFiles(string $model): string
    {
        return $model::$fileExtension;
    }

    /**
     * Get the source directory path of a model.
     * 
     * This is what powers the Hyde autodiscovery.
     */
    public static function getFilePathForModelClassFiles(string $model): string
    {
        return $model::$sourceDirectory;
    }

    /**
     * Determine the Page Model to use for a given file path.
     *
     * @return string The model class constant, or false if none was found.
     */
    public static function findModelFromFilePath(string $filepath): string|false
    {
        if (str_starts_with($filepath, '_posts')) {
            return MarkdownPost::class;
        }

        if (str_starts_with($filepath, '_docs')) {
            return DocumentationPage::class;
        }

        if (str_starts_with($filepath, '_pages') && str_ends_with($filepath, '.md')) {
            return MarkdownPage::class;
        }

        if (str_starts_with($filepath, '_pages') && str_ends_with($filepath, '.blade.php')) {
            return BladePage::class;
        }

        return false;
    }

    /**
     * Create a filepath that can be opened in the browser from a terminal.
     *
     * @param  string  $filepath
     * @return string
     */
    public static function createClickableFilepath(string $filepath): string
    {
        return 'file://'.str_replace(
            '\\',
            '/',
            realpath($filepath)
        );
    }
}
