<?php

namespace Hyde\Framework\Services\Markdown;

use Hyde\Framework\Contracts\MarkdownProcessorContract;

/**
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

        $this->shortcodes = $this->discoverShortcodes();
    }

    public function processInput(): self
    {
        $this->output = implode("\n", array_map(function ($line) {
            return $this->resolveShortcode($line);
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

    protected function discoverShortcodes(): array
    {
        $shortcodes = [];

        // Add default shortcodes @todo make this configurable
        foreach (glob(__DIR__.'/shortcodes/*.php') as $file) {
            $class = 'Hyde\Framework\Services\Markdown\Shortcodes\\'. str_replace('.php', '', basename($file));

            if (class_exists($class) && is_subclass_of($class, \Hyde\Framework\Contracts\MarkdownShortcodeContract::class)) {
                $shortcodes[] = $class;
            }
        }

        return $shortcodes;
    }

    protected function resolveShortcode(string $line): string
    {
        // If line matches a shortcode directive, resolve it and return the result.

        // Else, return the line as-is.
        return $line;
    }
}