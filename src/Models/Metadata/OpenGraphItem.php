<?php

namespace Hyde\Framework\Models\Metadata;

use Hyde\Framework\Contracts\MetadataItemContract;

class OpenGraphItem implements MetadataItemContract, \Stringable
{
    public function __construct(protected string $property, protected string $content)
    {
        $this->normalizeProperty();
    }

    public function __toString(): string
    {
        return '<meta property="'.e($this->property).'" content="'.e($this->content).'">';
    }

    public function uniqueKey(): string
    {
        return substr($this->property, 3);
    }

    protected function normalizeProperty(): void
    {
        $this->property = str_starts_with($this->property, 'og:') ? $this->property : 'og:'.$this->property;
    }
}
