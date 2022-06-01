<?php

namespace Hyde\Framework\Services;

use Hyde\Framework\Concerns\Markdown\HasConfigurableMarkdownFeatures;
use Hyde\Framework\Concerns\Markdown\HasTorchlightIntegration;
use Hyde\Framework\Services\Markdown\ShortcodeProcessor;
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use Torchlight\Commonmark\V2\TorchlightExtension;

/**
 * Interface for the CommonMarkConverter,
 * allowing for easy configuration of extensions.
 *
 * @see \Tests\Feature\MarkdownConverterServiceTest
 * @see \Tests\Feature\Services\HasConfigurableMarkdownFeaturesTest
 */
class MarkdownConverterService
{
    use HasConfigurableMarkdownFeatures;
    use HasTorchlightIntegration;

    public string $markdown;
    public ?string $sourceModel = null;

    protected array $config = [];
    protected array $extensions = [];
    protected CommonMarkConverter $converter;

    protected string $html;

    public function __construct(string $markdown, ?string $sourceModel = null)
    {
        $this->sourceModel = $sourceModel;
        $this->markdown = $markdown;
    }

    public function parse(): string
    {
        $this->setupConverter();

        $this->runPreprocessing();

        $this->html = $this->converter->convert($this->markdown);

        $this->runPostProcessing();

        return $this->html;
    }

    public function addExtension(string $extensionClassName): void
    {
        if (! in_array($extensionClassName, $this->extensions)) {
            $this->extensions[] = $extensionClassName;
        }
    }

    public function initializeExtension(string $extensionClassName): void
    {
        $this->converter->getEnvironment()->addExtension(new $extensionClassName());
    }

    protected function setupConverter(): void
    {
        // Determine what dynamic extensions to enable

        if ($this->canEnablePermalinks()) {
            $this->addExtension(HeadingPermalinkExtension::class);

            $this->config = array_merge([
                'heading_permalink' =>[
                    'id_prefix' => '',
                    'fragment_prefix' => '',
                    'symbol' => '',
                ],
            ], $this->config);
        }

        if ($this->canEnableTorchlight()) {
            $this->addExtension(TorchlightExtension::class);
        }

        // Add any custom extensions defined in config
        foreach (config('markdown.extensions', []) as $extensionClassName) {
            $this->addExtension($extensionClassName);
        }

        // Merge any custom configuration options
        $this->config = array_merge(config('markdown.config', []), $this->config);

        $this->converter = new CommonMarkConverter($this->config);

        foreach ($this->extensions as $extension) {
            $this->initializeExtension($extension);
        }
    }

    protected function runPreprocessing(): void
    {
        // Run any pre-processing actions
        if (config('markdown.enable_blade', false)) {
            $this->markdown = BladeDownService::preprocess($this->markdown);
        }

        $this->markdown = ShortcodeProcessor::process($this->markdown);
    }

    protected function runPostProcessing(): void
    {
        // Run any post-processing actions
        if ($this->determineIfTorchlightAttributionShouldBeInjected()) {
            $this->html .= $this->injectTorchlightAttribution();
        }

        if (config('markdown.enable_blade', false)) {
            $this->html = (new BladeDownService($this->html))->process()->get();
        }

        $this->html = $this->expandCodeblocksFilepathLabel($this->html);
    }

    // Helper to inspect the currently enabled extensions
    public function getExtensions(): array
    {
        return $this->extensions;
    }

    public function expandCodeblocksFilepathLabel(string $html): string
    {
        return implode("\n", array_map(function ($line) {
            if (str_starts_with(strtolower($line), '<pre><code class="language-markdown">// filepath: ')) {
                $line = str_replace('// Filepath: ', '<small class="filepath">', $line);
                return $line . '</small>';
            }
            return $line;
        }, explode("\n", $html)));
    }
}
