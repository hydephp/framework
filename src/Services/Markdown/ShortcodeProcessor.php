<?php

namespace Hyde\Framework\Services\Markdown;

use Hyde\Framework\Contracts\MarkdownProcessorContract;

class ShortcodeProcessor implements MarkdownProcessorContract
{
    protected string $input;
    protected string $output;

    public function __construct(string $input)
    {
        $this->input = $input;
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

    protected function resolveShortcode(string $line): string
    {
        // If line matches a shortcode directive, resolve it and return the result.

        // Else, return the line as-is.
        return $line;
    }
}