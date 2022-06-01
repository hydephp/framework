<?php

namespace Hyde\Framework\Services\Markdown;

class AddFilepathLabelToCodeblockPostProcessor
{
    public static function process(string $html): string
    {
        return implode("\n", array_map(function ($line) {
            if (str_starts_with(strtolower($line), '<pre><code class="language-markdown">// filepath: ')) {
                $line = str_replace('// Filepath: ', '<small class="filepath"><span class="sr-only">Filepath: </span>', $line);
                return $line . '</small>';
            }
            return $line;
        }, explode("\n", $html)));
    }
}