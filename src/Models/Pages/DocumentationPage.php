<?php

namespace Hyde\Framework\Models\Pages;

use Hyde\Framework\Concerns\HasTableOfContents;
use Hyde\Framework\Contracts\AbstractMarkdownPage;
use Hyde\Framework\Hyde;
use Hyde\Framework\Models\Parsers\DocumentationPageParser;

class DocumentationPage extends AbstractMarkdownPage
{
    use HasTableOfContents;

    public static string $sourceDirectory = '_docs';
    public static string $outputDirectory = 'docs';

    public static string $parserClass = DocumentationPageParser::class;

    public function __construct(array $matter = [], string $body = '', string $title = '', string $slug = '')
    {
        parent::__construct($matter, $body, $title, $slug);

        $this->constructTableOfContents();
    }

    /** @internal */
    public function getOnlineSourcePath(): string|false
    {
        if (config('docs.source_file_location_base') === null) {
            return false;
        }

        return trim(config('docs.source_file_location_base'), '/').'/'.$this->slug.'.md';
    }

    /**
     * Get the path to the frontpage for the documentation.
     *
     * It is highly recommended to have an index.md file in the _docs directory,
     * however, this method will fall back to a readme.
     *
     * @since 0.46.x (moved from Hyde::docsIndexPath).
     *
     * @return string|false returns false if no suitable frontpage is found
     */
    public static function indexPath(): string|false
    {
        if (file_exists(Hyde::path(static::getSourceDirectory().'/index.md'))) {
            return trim(Hyde::pageLink(static::getOutputDirectory().'/index.html'), '/');
        }

        if (file_exists(Hyde::path(static::getSourceDirectory().'/readme.md'))) {
            return trim(Hyde::pageLink(static::getOutputDirectory().'/readme.html'), '/');
        }

        return false;
    }
}
