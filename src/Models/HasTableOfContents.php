<?php

namespace Hyde\Framework\Models;

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
        // @todo add feature to disable table of contents
        // if Features::withTableOfContents
        $this->tableOfContents = (new GeneratesTableOfContents($this->body))->execute();
    }
}