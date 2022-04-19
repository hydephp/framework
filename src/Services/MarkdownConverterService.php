<?php

namespace Hyde\Framework\Services;

use Hyde\Framework\Features;
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use Torchlight\Commonmark\V2\TorchlightExtension;

class MarkdownConverterService
{
    public string $markdown;

    protected bool $useTorchlight;
    protected bool $torchlightAttribution;

    protected CommonMarkConverter $converter;

    protected string $html;

    public function __construct(string $markdown, ?bool $useTorchlight = null, ?bool $torchlightAttribution = null)
    {
        $this->markdown = $markdown;

        $config = [];
        if (config('hyde.documentationPageTableOfContents.enabled', true)) {
            $config = array_merge([
                'heading_permalink' =>[
                    'id_prefix' => '',
                    'fragment_prefix' => '',
                    'symbol' => ''
                ],
            ], $config);
        }

        $this->converter = new CommonMarkConverter($config);
        $this->converter->getEnvironment()->addExtension(new GithubFlavoredMarkdownExtension());

        if (config('hyde.documentationPageTableOfContents.enabled', true)) {
            $this->converter->getEnvironment()->addExtension(new HeadingPermalinkExtension());
        }

        $this->useTorchlight = $useTorchlight ?? $this->determineIfTorchlightShouldBeEnabled();
    }

    public function parse(): string
    {
        if ($this->useTorchlight) {
            $this->converter->getEnvironment()->addExtension(new TorchlightExtension());
        }

        $this->html = $this->converter->convert($this->markdown);

        $this->torchlightAttribution = $torchlightAttribution ?? $this->determineIfTorchlightAttributionShouldBeInjected();

        if ($this->torchlightAttribution) {
            $this->html .= $this->injectTorchlightAttribution();
        }

        return $this->html;
    }

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
