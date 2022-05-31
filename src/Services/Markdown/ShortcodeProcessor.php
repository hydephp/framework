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
        $this->output = $this->input;
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
}