<?php

namespace Hyde\Framework\Modules\DataCollections;

use Hyde\Framework\Hyde;
use Hyde\Framework\Models\MarkdownDocument;
use Hyde\Framework\Services\MarkdownFileService;
use Illuminate\Support\Collection;

/**
 * Automatically generates Laravel Collections from static data files,
 * such as Markdown components and YAML files using Hyde Autodiscovery.
 *
 * @see \Hyde\Framework\Testing\Feature\DataCollectionTest
 */
class DataCollection extends Collection
{
    public string $key;

    protected float $timeStart;
    public float $parseTimeInMs;

    public static string $sourceDirectory = '_data';

    public function __construct(string $key)
    {
        $this->timeStart = microtime(true);
        $this->key = $key;

        parent::__construct();
    }

    public function getCollection(): DataCollection
    {
        $this->parseTimeInMs = round((microtime(true) - $this->timeStart) * 1000, 2);
        unset($this->timeStart);

        return $this;
    }

    public function getMarkdownFiles(): array
    {
        return glob(Hyde::path(
            static::$sourceDirectory.'/'.$this->key.'/*.md'
        ));
    }

    /**
     * Get a collection of Markdown documents in the _data/<$key> directory.
     * Each Markdown file will be parsed into a MarkdownDocument with front matter.
     *
     * @param  string  $key  for a subdirectory of the _data directory
     * @return DataCollection<\Hyde\Framework\Models\MarkdownDocument>
     */
    public static function markdown(string $key): DataCollection
    {
        $collection = new DataCollection($key);
        foreach ($collection->getMarkdownFiles() as $file) {
            $collection->push(
                (new MarkdownFileService($file))->get()
            );
        }

        return $collection->getCollection();
    }
}
