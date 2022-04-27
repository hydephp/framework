<?php

namespace Hyde\Framework\Concerns;

use Hyde\Framework\Actions\GeneratesTableOfContents;
use function config;

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
