<?php

namespace Hyde\Framework\Concerns;

use function config;
use Hyde\Framework\Actions\GeneratesTableOfContents;

/**
 * Trait HasTableOfContents.
 *
 * @see \Tests\Unit\HasTableOfContentsTest
 */
trait HasTableOfContents
{
    public string $tableOfContents;

    public function constructTableOfContents(): void
    {
        if (config('hyde.documentationPageTableOfContents.enabled', true)) {
            $this->tableOfContents = (new GeneratesTableOfContents($this->body))->execute();
        }
    }
}
