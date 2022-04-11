<?php

namespace Hyde\Framework\Models;

use JetBrains\PhpStorm\ArrayShape;

/**
 * Metadata class for storing metadata about a model.
 * Is used in Blade views to create <meta> tags.
 */
class Metadata
{
    #[ArrayShape(['name' => 'content'])]
    public array $metadata = [];

    #[ArrayShape(['property' => 'content'])]
    public array $properties = [];

    public function __construct(array $metadata = [], array $properties = [])
    {
        $this->metadata = $metadata;
        $this->properties = $properties;
    }

    public function add(string $name, string $content): self
    {
        $this->metadata[$name] = $content;

        return $this;
    }

    public function addProperty(string $property, string $content): self
    {
        $this->properties[$property] = $content;

        return $this;
    }
}
