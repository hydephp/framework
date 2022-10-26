<?php

declare(strict_types=1);

namespace Hyde\Framework\Modules\Metadata\Models;

use Hyde\Framework\Modules\Metadata\MetadataElementContract;

class OpenGraphElement implements MetadataElementContract
{
    protected string $property;
    protected string $content;

    public function __construct(string $property, string $content)
    {
        $this->property = $this->normalizeProperty($property);
        $this->content = $content;
    }

    public function __toString(): string
    {
        return sprintf('<meta property="og:%s" content="%s">', e($this->property), e($this->content));
    }

    public function uniqueKey(): string
    {
        return $this->property;
    }

    protected function normalizeProperty(string $property): string
    {
        return str_starts_with($property, 'og:') ? substr($property, 3) : $property;
    }
}
