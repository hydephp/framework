<?php

namespace Hyde\Framework\Actions\ServiceActions;

use Hyde\Framework\Features;
use Hyde\Framework\Markdown;
use Hyde\Framework\Models\DocumentationPage;

/**
 * Allow the Markdown service to have configurable features.
 *
 * @see HasMarkdownFeatures for global feature management.
 */
trait HasConfigurableMarkdownFeatures
{
    protected array $features = [];

    public function addFeature(string $feature): self
    {
        if (! in_array($feature, $this->features)) {
            $this->features[] = $feature;
        }

        return $this;
    }

    public function hasFeature(string $feature): bool
    {
        return in_array($feature, $this->features);
    }

    public function withTableOfContents(): self
    {
        $this->addFeature('table-of-contents');

        return $this;
    }

    public function withPermalinks(): self
    {
        $this->addFeature('permalinks');

        return $this;
    }

    public function canEnablePermalinks(): bool
    {
        return $this->hasFeature('permalinks')
            || $this->sourceModel === DocumentationPage::class && Markdown::hasTableOfContents();
    }

    public function canEnableTorchlight(): bool
    {
        return $this->hasFeature('torchlight') ||
                    Features::hasTorchlight();
    }
}
