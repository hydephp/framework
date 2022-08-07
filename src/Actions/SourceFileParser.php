<?php

namespace Hyde\Framework\Actions;

use Hyde\Framework\Concerns\ValidatesExistence;
use Hyde\Framework\Contracts\AbstractMarkdownPage;
use Hyde\Framework\Contracts\PageContract;
use Hyde\Framework\Models\Pages\BladePage;
use Hyde\Framework\Modules\Markdown\MarkdownFileParser;

/**
 * Parses a source file and returns a new page model instance for it.
 *
 * Page Parsers are responsible for parsing a source file into a Page object,
 * and may also conduct pre-processing and/or data validation/assembly.
 *
 * Note that the Page Parsers do not compile any HTML or Markdown.
 *
 * @see \Hyde\Framework\Testing\Feature\SourceFileParserTest
 */
class SourceFileParser
{
    use ValidatesExistence;

    protected string $slug;
    protected PageContract $page;

    public function __construct(string $pageClass, string $slug)
    {
        $this->validateExistence($pageClass, $slug);
        $this->slug = $slug;

        $this->page = $this->constructBaseModel($pageClass);
    }

    protected function constructBaseModel(string $pageClass): BladePage|AbstractMarkdownPage
    {
        return $pageClass === BladePage::class
            ? $this->parseBladePage()
            : $this->parseMarkdownPage($pageClass);
    }

    protected function parseBladePage(): BladePage
    {
        return new BladePage($this->slug,
            $this->parseBladeMatter(file_get_contents(BladePage::qualifyBasename($this->slug)))
        );
    }

    protected function parseBladeMatter(string $contents): array
    {
        return (new BladeMatterParser($contents))->parse()->get();
    }

    protected function parseMarkdownPage(string $pageClass): AbstractMarkdownPage
    {
        /** @var AbstractMarkdownPage $pageClass */
        $document = MarkdownFileParser::parse(
            $pageClass::qualifyBasename($this->slug)
        );

        $matter = $document->matter;
        $markdown = $document->markdown;

        return new $pageClass(
            identifier: $this->slug,
            matter: $matter,
            markdown: $markdown
        );
    }

    public function get(): PageContract
    {
        return $this->page;
    }
}
