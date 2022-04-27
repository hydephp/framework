<?php

namespace Hyde\Framework\Concerns\Markdown;

use function config;

trait HasTorchlightIntegration
{
    protected bool $useTorchlight;
    protected bool $torchlightAttribution;

    protected function determineIfTorchlightAttributionShouldBeInjected(): bool
    {
        return config('torchlight.attribution.enabled', true)
            && str_contains($this->html, 'Syntax highlighted by torchlight.dev');
    }

    protected function injectTorchlightAttribution(): string
    {
        return $this->converter->convert(config(
            'torchlight.attribution.markdown',
            'Syntax highlighted by torchlight.dev'
        ));
    }
}
