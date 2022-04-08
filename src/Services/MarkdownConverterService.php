<?php

namespace Hyde\Framework\Services;

use Hyde\Framework\Features;
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
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

		$this->converter = new CommonMarkConverter();
		$this->converter->getEnvironment()->addExtension(new GithubFlavoredMarkdownExtension());

		$this->useTorchlight = $useTorchlight ?? $this->determineIfTorchlightShouldBeEnabled();
		$this->torchlightAttribution = $torchlightAttribution ?? $this->determineIfTorchlightAttributionShouldBeInjected();
	}

	public function parse(): string
	{
		if ($this->useTorchlight) {
			$this->converter->getEnvironment()->addExtension(new TorchlightExtension());
		}

		$this->html = $this->converter->convert($this->markdown);

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
