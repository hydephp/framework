<?php

namespace Hyde\Framework\Actions;

use Hyde\Framework\Contracts\ActionContract;
use Hyde\Framework\Hyde;
use Hyde\Framework\Models\DocumentationPage;
use Hyde\Framework\Models\Parsers\DocumentationPageParser;
use Hyde\Framework\Services\CollectionService;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Generate a JSON file that can be used as a search index for documentation pages.
 *
 * @see \Tests\Feature\Actions\GeneratesDocumentationSearchIndexFileTest
 */
class GeneratesDocumentationSearchIndexFile implements ActionContract
{
    public Collection $searchIndex;
    public static string $filePath = '_site/docs/search.json';

    public static function run(): void
    {
        (new static())->execute();
    }

    public function __construct()
    {
        $this->searchIndex = new Collection();
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
            'content' => $this->getSearchContentForDocument($page),
        ];
    }

    public function getSourceFileSlugs(): array
    {
        return CollectionService::getDocumentationPageList();
    }

    public function getObject(): object
    {
        return (object) $this->searchIndex;
    }

    public function getJson(): string
    {
        return json_encode($this->getObject(), JSON_PRETTY_PRINT);
    }

    public function save(): self
    {
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
}