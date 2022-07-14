<?php

namespace Hyde\Framework\Services;

use Hyde\Framework\Concerns\Markdown\HasConfigurableMarkdownFeatures;
use Hyde\Framework\Concerns\Markdown\HasTorchlightIntegration;
use Hyde\Framework\Services\Markdown\BladeDownProcessor;
use Hyde\Framework\Services\Markdown\CodeblockFilepathProcessor;
use Hyde\Framework\Services\Markdown\ShortcodeProcessor;
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Extension\DisallowedRawHtml\DisallowedRawHtmlExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use Torchlight\Commonmark\V2\TorchlightExtension;

/**
 * Interface for the CommonMarkConverter,
 * allowing for easy configuration of extensions.
 *
 * @see \Hyde\Framework\Testing\Feature\MarkdownConverterServiceTest
 * @see \Hyde\Framework\Testing\Feature\Services\HasConfigurableMarkdownFeaturesTest
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
                    'symbol' => '#',
                    'insert' => 'after',
                    'min_heading_level' => 2,
                ],
            ], $this->config);
        }

        if ($this->canEnableTorchlight()) {
            $this->addExtension(TorchlightExtension::class);
        }

        if (config('markdown.allow_html', false)) {
            $this->addExtension(DisallowedRawHtmlExtension::class);

            $this->config = array_merge([
                'disallowed_raw_html' => [
                    'disallowed_tags' => [],
                ],
            ], $this->config);
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
        if (config('markdown.enable_blade', false)) {
            $this->markdown = BladeDownProcessor::preprocess($this->markdown);
        }

        $this->markdown = ShortcodeProcessor::process($this->markdown);

        $this->markdown = CodeblockFilepathProcessor::preprocess($this->markdown);
    }

    protected function runPostProcessing(): void
    {
        if ($this->determineIfTorchlightAttributionShouldBeInjected()) {
            $this->html .= $this->injectTorchlightAttribution();
        }

        if (config('markdown.enable_blade', false)) {
            $this->html = BladeDownProcessor::process($this->html);
        }

        if (config('markdown.features.codeblock_filepaths', true)) {
            $this->html = CodeblockFilepathProcessor::process($this->html);
        }

        // Remove any Hyde annotations (everything between `// HYDE!` and `HYDE! //`) (must be done last)
        $this->html = preg_replace('/ \/\/ HYDE!.*HYDE! \/\//s', '', $this->html);
    }

    public function getExtensions(): array
    {
        return $this->extensions;
    }
}
