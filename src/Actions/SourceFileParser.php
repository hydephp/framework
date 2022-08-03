<?php

namespace Hyde\Framework\Actions;

use Hyde\Framework\Concerns\ValidatesExistence;
use Hyde\Framework\Contracts\AbstractMarkdownPage;
use Hyde\Framework\Contracts\PageContract;
use Hyde\Framework\Hyde;
use Hyde\Framework\Models\Pages\BladePage;
use Hyde\Framework\Models\Pages\DocumentationPage;
use Hyde\Framework\Modules\Markdown\MarkdownFileParser;
use Illuminate\Support\Str;

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
        $this->constructDynamicData();
    }

    protected function constructBaseModel(string $pageClass): BladePage|AbstractMarkdownPage
    {
        return $pageClass === BladePage::class
            ? $this->parseBladePage()
            : $this->parseMarkdownPage($pageClass);
    }

    protected function parseBladePage(): BladePage
    {
        return new BladePage($this->slug);
    }

    protected function parseMarkdownPage(string $pageClass): AbstractMarkdownPage
    {
        /** @var AbstractMarkdownPage $pageClass */
        $document = MarkdownFileParser::parse(
            $pageClass::qualifyBasename($this->slug)
        );

        $matter = $document->matter;
        $body = $document->body;

        return new $pageClass(
            matter: $matter,
            body: $body,
            slug: $this->slug
        );
    }

    protected function constructDynamicData(): void
    {
        $this->page->title = $this->findTitleForPage();

        if ($this->page instanceof DocumentationPage) {
            $this->page->category = $this->getDocumentationPageCategory();
        }
    }

    protected function findTitleForPage(): string
    {
        if ($this->page instanceof BladePage) {
            return Hyde::makeTitle($this->slug);
        }

        if ($this->page->matter('title')) {
            return $this->page->matter('title');
        }

        return $this->findTitleFromMarkdownHeadings() ?? Hyde::makeTitle($this->slug);
    }

    protected function findTitleFromMarkdownHeadings(): ?string
    {
        foreach ($this->page->markdown()->toArray() as $line) {
            if (str_starts_with($line, '# ')) {
                return trim(substr($line, 2), ' ');
            }
        }

        return null;
    }

    protected function getDocumentationPageCategory(): ?string
    {
        // If the documentation page is in a subdirectory,
        // then we can use that as the category name.
        // Otherwise, we look in the front matter.

        return str_contains($this->slug, '/')
            ? Str::before($this->slug, '/')
            : $this->page->matter('category');
    }

    public function get(): PageContract
    {
        return $this->page;
    }
}
