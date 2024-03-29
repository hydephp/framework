<?php

declare(strict_types=1);

namespace Hyde\Pages;

use Hyde\Pages\Concerns\HydePage;

/**
 * Page class for HTML pages.
 *
 * Html pages are stored in the _pages directory and using the .html extension.
 * These pages will be copied exactly as they are to the _site/ directory.
 *
 * @see https://hydephp.com/docs/1.x/static-pages#bonus-creating-html-pages
 */
class HtmlPage extends HydePage
{
    public static string $sourceDirectory = '_pages';
    public static string $outputDirectory = '';
    public static string $fileExtension = '.html';

    public function contents(): string
    {
        return file_get_contents($this->getSourcePath());
    }

    public function compile(): string
    {
        return $this->contents();
    }

    public function getBladeView(): string
    {
        return $this->getSourcePath();
    }
}
