<?php

namespace Hyde\Framework\Actions;

use Hyde\Framework\Contracts\ActionContract;
use Hyde\Framework\Hyde;
use Hyde\Framework\Models\Parsers\DocumentationPageParser;
use Illuminate\Support\Collection;

/**
 * Generate a JSON file that can be used as a search index for documentation pages.
 *
 * @see \Tests\Feature\Actions\GeneratesDocumentationSearchIndexFileTest
 */
class GeneratesDocumentationSearchIndexFile implements ActionContract
{
    public Collection $searchIndex;
    public static string $filePath = '_site/docs/searchIndex.json';

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

    public function generate(): void
    {
        foreach ($this->getSourceFileSlugs() as $page) {
            $this->searchIndex->push(
                $this->generatePageObject($page)
            );
        }
    }

    public function generatePageObject(string $slug): object
    {
        $page = (new DocumentationPageParser($slug))->get();

        return (object) [
            'slug' => $page->slug,
            'title' => $page->title,
        ];
    }

    public function getSourceFileSlugs(): array
    {
        return [];
    }

    public function getObject(): object
    {
        return (object) $this->searchIndex;
    }

    public function getJson(): string
    {
        return json_encode($this->getObject());
    }

    public function save(): void
    {
        file_put_contents(Hyde::path(static::$filePath), $this->getJson());
    }
}