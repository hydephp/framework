<?php

namespace Hyde\Framework\Actions;

use Hyde\Framework\Concerns\InteractsWithDirectories;
use Hyde\Framework\Contracts\ActionContract;
use Hyde\Framework\Hyde;
use Hyde\Framework\Models\Pages\DocumentationPage;
use Hyde\Framework\Models\Parsers\DocumentationPageParser;
use Hyde\Framework\Services\CollectionService;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Generate a JSON file that can be used as a search index for documentation pages.
 *
 * @todo Convert into Service, and add more strategies, such as slug-only (no file parsing)
 *        search which while dumber, would be much faster to compile and take way less space.
 * @todo Refactor to use custom site output paths
 *
 * @see \Hyde\Framework\Testing\Feature\Actions\GeneratesDocumentationSearchIndexFileTest
 *
 * @phpstan-consistent-constructor
 */
class GeneratesDocumentationSearchIndexFile implements ActionContract
{
    use InteractsWithDirectories;

    public Collection $searchIndex;
    public static string $filePath = '_site/docs/search.json';

    public static function run(): void
    {
        (new static())->execute();
    }

    public function __construct()
    {
        $this->searchIndex = new Collection();

        static::$filePath = '_site/'.config('docs.output_directory', 'docs').'/search.json';
    }

    public function execute(): void
    {
        $this->generate();
        $this->save();
    }

    public function generate(): self
    {
        foreach ($this->getSourceFileSlugs() as $page) {
            $this->searchIndex->push(
                $this->generatePageObject($page)
            );
        }

        return $this;
    }

    public function generatePageObject(string $slug): object
    {
        $page = (new DocumentationPageParser($slug))->get();

        return (object) [
            'slug' => $page->slug,
            'title' => trim($page->findTitleForDocument()),
            'content' => trim($this->getSearchContentForDocument($page)),
            'destination' => $this->getDestinationForSlug($page->slug),
        ];
    }

    public function getSourceFileSlugs(): array
    {
        return array_diff(
            CollectionService::getDocumentationPageFiles(),
            config('docs.exclude_from_search', [])
        );
    }

    public function getObject(): object
    {
        return (object) $this->searchIndex;
    }

    public function getJson(): string
    {
        return json_encode($this->getObject());
    }

    public function save(): self
    {
        $this->needsDirectory(Hyde::path(str_replace('/search.json', '', static::$filePath)));

        file_put_contents(Hyde::path(static::$filePath), $this->getJson());

        return $this;
    }

    /**
     * There are a few ways we could go about this. The goal is to allow the user
     * to run a free-text search to find relevant documentation pages.
     *
     * The easiest way to do this is by adding the Markdown body to the search index.
     * But this is of course not ideal as it may take an incredible amount of space
     * for large documentation sites. The Hyde docs weight around 80kb of JSON.
     *
     * Another option is to assemble all the headings in a document and use that
     * for the search basis. A truncated version of the body could also be included.
     *
     * A third option which might be the most space efficient (besides from just
     * adding titles, which doesn't offer much help to the user since it is just
     * a filterable sidebar at that point), would be to search for keywords
     * in the document. This would however add complexity as well as extra
     * computing time.
     *
     * Benchmarks: (for official Hyde docs)
     *
     * Returning $document->body as is: 500ms
     * Returning $document->body as Str::markdown(): 920ms + 10ms for regex
     */
    public function getSearchContentForDocument(DocumentationPage $document): string
    {
        // This is compiles the Markdown body into HTML, and then strips out all
        // HTML tags to get a plain text version of the body. This takes a long
        // site, but is the simplest implementation I've found so far.
        return preg_replace('/<(.|\n)*?>/', ' ', Str::markdown($document->body));
    }

    public function getDestinationForSlug(string $slug): string
    {
        if ($slug === 'index' && config('site.pretty_urls', false)) {
            $slug = '';
        }

        return (config('site.pretty_urls', false) === true)
            ? $slug : $slug.'.html';
    }
}
