<?php

namespace Hyde\Framework\Concerns;

use Hyde\Framework\Actions\GeneratesTableOfContents;

/**
 * Generate a Table of Contents HTML string from a Markdown document body.
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
