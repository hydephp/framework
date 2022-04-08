<?php

namespace Hyde\Framework\Services;

use Hyde\Framework\DocumentationPageParser;
use Hyde\Framework\MarkdownPageParser;
use Hyde\Framework\MarkdownPostParser;
use Hyde\Framework\Models\BladePage;
use Hyde\Framework\Models\DocumentationPage;
use Hyde\Framework\Models\MarkdownPage;
use Hyde\Framework\Models\MarkdownPost;

/**
 * Static service helpers for building static pages.
 */
class BuildService
{
    public static function getParserClassForModel(string $model): string|false
    {
        try {
            return $model::$parserClass;
        } catch (\Error) {
            return false;
        }
    }

    /**
     * Create and get a constructed instance of a Model's Parser class.
     *
     * @param string $model Class constant of the Model to get the Parser for.
     * @param string $slug The slug of the source file to parse.
     * 
     * @example getParserForModel(MarkdownPost::class, 'hello-world')
     * 
     * @return object|false The constructed Parser instance, or false if the Model is not valid.
     */
    public static function getParserInstanceForModel(string $model, string $slug): object|false
    {
        try {
            return new $model::$parserClass($slug);
        } catch (\Error) {
            return false;
        }
    }

    /**
     * Get the file extension for a models source files.
     */
    public static function getFileExtensionForModelFiles(string $model): string|false
    {
        try {
            return $model::$fileExtension;
        } catch (\Error) {
            return false;
        }
    }

    /**
     * Get the source directory path of a model.
     */
    public static function getFilePathForModelClassFiles(string $model): string|false
    {
        try {
            return $model::$sourceDirectory;
        } catch (\Error) {
            return false;
        }
    }

     /**
     * Determine the Page Model to use for a given file path.
     * 
     * @return string|false The model class constant, or false if none was found.
     */
    public static function findModelFromFilePath(string $filepath): string|false
    {
        if (str_starts_with($filepath, '_posts')) {
            return MarkdownPost::class;
        }

        if (str_starts_with($filepath, '_pages')) {
            return MarkdownPage::class;
        }

        if (str_starts_with($filepath, '_docs')) {
            return DocumentationPage::class;
        }

        if (str_starts_with($filepath, 'resources/views/pages')) {
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
        return 'file://' . str_replace(
            '\\',
            '/',
            realpath($filepath)
        );
    }
}
