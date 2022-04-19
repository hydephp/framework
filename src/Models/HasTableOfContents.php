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
        $this->tableOfContents = (new GeneratesTableOfContents($this->body))->execute();
    }
}