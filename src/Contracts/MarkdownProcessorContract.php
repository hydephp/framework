<?php

namespace Hyde\Framework\Contracts;

interface MarkdownProcessorContract
{
    /**
     * @param string $input Markdown/HTML to be processed
     * @return string $output Processed Markdown/HTML output
     */
    public static function process(string $input): string;
}