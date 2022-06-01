<?php

namespace Hyde\Framework\Services\Markdown;

use Hyde\Framework\Contracts\MarkdownProcessorContract;
use Hyde\Framework\Contracts\MarkdownShortcodeContract;
use Hyde\Framework\Services\Markdown\Shortcodes\AbstractColoredBlockquote;

/**
 * Handle shortcode processing for Markdown conversions.
 *
 * @todo Refactor shortcode manager to singleton as it does not need to be re-instantiated
 *      for each Markdown conversion.
 *
 * @see \Tests\Feature\Services\Markdown\ShortcodeProcessorTest
 */
class ShortcodeProcessor implements MarkdownProcessorContract
{
    protected string $input;
    protected string $output;

    public array $shortcodes;

    public function __construct(string $input)
    {
        $this->input = $input;

        $this->discoverShortcodes();
    }

    public function processInput(): self
    {
        $this->output = implode("\n", array_map(function ($line) {
            return $this->expandShortcode($line);
        }, explode("\n", $this->input)));

        return $this;
    }

    public function getOutput(): string
    {
        return $this->output;
    }

    public static function process(string $input): string
    {
        return (new static($input))->processInput()->getOutput();
    }

    protected function discoverShortcodes(): void
    {
        // Discover default shortcodes @todo make this configurable
        foreach (glob(__DIR__.'/shortcodes/*.php') as $file) {
            $class = 'Hyde\Framework\Services\Markdown\Shortcodes\\'. str_replace('.php', '', basename($file));

            if (class_exists($class)
                && is_subclass_of($class, MarkdownShortcodeContract::class)
                && ! str_starts_with(basename($file), 'Abstract')) {
                $this->addShortcode(new $class());
            }
        }

        // Register any provided shortcodes
        $this->addShortcodesFromArray(AbstractColoredBlockquote::get());
    }

    public function addShortcodesFromArray(array $shortcodes): self
    {
        foreach ($shortcodes as $shortcode) {
            $this->addShortcode($shortcode);
        }

        return $this;
    }

    public function addShortcode(MarkdownShortcodeContract $shortcode): self
    {
        $this->shortcodes[$shortcode::signature()] = $shortcode;

        return $this;
    }

    protected function expandShortcode(string $line): string
    {
        // If line matches a shortcode directive, resolve it and return the result.
        if (array_key_exists($signature = substr($line, 0, strpos($line, ' ')), $this->shortcodes)) {
            return $this->shortcodes[$signature]::resolve($line);
        }

        // Else, return the line as-is.
        return $line;
    }
}