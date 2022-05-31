<?php

namespace Hyde\Framework\Services\Markdown;

use Hyde\Framework\Contracts\MarkdownProcessorContract;

class ShortcodeProcessor implements MarkdownProcessorContract
{
    public static function process(string $input): string
    {
        return (new static($input))->processInput()->getOutput();
    }
}