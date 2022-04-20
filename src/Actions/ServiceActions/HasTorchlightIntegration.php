<?php

namespace Hyde\Framework\Actions\ServiceActions;

use Hyde\Framework\Features;

trait HasTorchlightIntegration
{
    protected bool $useTorchlight;
    protected bool $torchlightAttribution;


    protected function determineIfTorchlightShouldBeEnabled(): bool
    {
        return Features::hasTorchlight();
    }

    protected function determineIfTorchlightAttributionShouldBeInjected(): bool
    {
        return $this->useTorchlight && config('torchlight.attribution.enabled', true)
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