<?php

declare(strict_types=1);

namespace Hyde\Framework\Features\Metadata;

use Hyde\Framework\Features\Metadata\Elements\LinkElement;
use Hyde\Framework\Features\Metadata\Elements\MetadataElement;
use Hyde\Framework\Features\Metadata\Elements\OpenGraphElement;
use Illuminate\Contracts\Support\Htmlable;
use function array_merge;
use function implode;

/**
 * Holds the metadata tags for a page or the site model.
 *
 * @see \Hyde\Framework\Testing\Feature\MetadataTest
 * @see \Hyde\Framework\Features\Metadata\PageMetadataBag
 * @see \Hyde\Framework\Features\Metadata\GlobalMetadataBag
 */
class MetadataBag implements Htmlable
{
    protected array $links = [];
    protected array $metadata = [];
    protected array $properties = [];
    protected array $generics = [];

    public function toHtml(): string
    {
        return $this->render();
    }

    public function render(): string
    {
        return implode("\n", $this->get());
    }

    public function get(): array
    {
        return array_merge(
            $this->getPrefixedArray('links'),
            $this->getPrefixedArray('metadata'),
            $this->getPrefixedArray('properties'),
            $this->getPrefixedArray('generics')
        );
    }

    public function add(MetadataElementContract|string $element): static
    {
        if ($element instanceof LinkElement) {
            return $this->addElement('links', $element);
        }

        if ($element instanceof MetadataElement) {
            return $this->addElement('metadata', $element);
        }

        if ($element instanceof OpenGraphElement) {
            return $this->addElement('properties', $element);
        }

        return $this->addGenericElement($element);
    }

    protected function addElement(string $type, MetadataElementContract $element): static
    {
        ($this->$type)[$element->uniqueKey()] = $element;

        return $this;
    }

    protected function addGenericElement(string $element): static
    {
        $this->generics[] = $element;

        return $this;
    }

    protected function getPrefixedArray(string $type): array
    {
        $array = [];

        /** @var MetadataElementContract $element */
        foreach ($this->{$type} as $key => $element) {
            $array["$type:$key"] = $element;
        }

        return $array;
    }
}
