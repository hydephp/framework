<?php

namespace Hyde\Framework\Models;

use JetBrains\PhpStorm\ArrayShape;

trait HasMetadata
{
    #[ArrayShape(['name' => "\content"])] function getMetadata(): array
    {
        return $this->metadata->metadata;
    }

    #[ArrayShape(['property' => "\content"])]
    function getMetaProperties(): array
    {
        return $this->metadata->properties;
    }
}