<?php

namespace Hyde\Framework\Services;

use Exception;
use Hyde\Framework\DocumentationPageParser;
use Hyde\Framework\MarkdownPageParser;
use Hyde\Framework\MarkdownPostParser;
use Hyde\Framework\Models\BladePage;
use Hyde\Framework\Models\DocumentationPage;
use Hyde\Framework\Models\MarkdownPage;
use Hyde\Framework\Models\MarkdownPost;
use Hyde\Framework\StaticPageBuilder;

/**
 * Static service helpers for building static pages.
 */
class BuildService
{
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

    public static function getParserClassForModel(string $model): string|false
    {
        if ($model === MarkdownPost::class) {
            return MarkdownPostParser::class;
        }

        if ($model === MarkdownPage::class) {
            return MarkdownPageParser::class;
        }

        if ($model === DocumentationPage::class) {
            return DocumentationPageParser::class;
        }

        if ($model === BladePage::class) {
            return BladePage::class;
        }

        return false;
    }

    public static function getParserInstanceForModel(string $model, string $slug): object|false
    {
        if ($model === MarkdownPost::class) {
            return new MarkdownPostParser($slug);
        }

        if ($model === MarkdownPage::class) {
            return new MarkdownPageParser($slug);
        }

        if ($model === DocumentationPage::class) {
            return new DocumentationPageParser($slug);
        }

        if ($model === BladePage::class) {
            return new BladePage($slug);
        }
    }

    public static function getFileExtensionForModelFiles(string $model): string|false
    {
        if ($model === MarkdownPost::class) {
            return '.md';
        }

        if ($model === MarkdownPage::class) {
            return '.md';
        }

        if ($model === DocumentationPage::class) {
            return '.md';
        }

        if ($model === BladePage::class) {
            return '.blade.php';
        }

        return false;
    }

    public static function getFilePathForModelClassFiles(string $model): string|false
    {
        if ($model === MarkdownPost::class) {
            return '_posts';
        }

        if ($model === MarkdownPage::class) {
            return '_pages';
        }

        if ($model === DocumentationPage::class) {
            return '_docs';
        }

        if ($model === BladePage::class) {
            return 'resources/views/pages';
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
