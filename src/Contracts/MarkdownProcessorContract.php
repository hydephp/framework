<?php

namespace Hyde\Framework\Contracts;

interface MarkdownProcessorContract
{
    public static function process(string $input): string;
}