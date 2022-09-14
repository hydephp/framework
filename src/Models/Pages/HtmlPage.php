<?php

namespace Hyde\Framework\Models\Pages;

use Hyde\Framework\Concerns\HydePage;

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
}
