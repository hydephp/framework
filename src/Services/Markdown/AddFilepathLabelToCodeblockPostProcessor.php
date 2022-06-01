<?php

namespace Hyde\Framework\Services\Markdown;

class AddFilepathLabelToCodeblockPostProcessor
{
    public static function process(string $html): string
    {
        $lines = explode("\n", $html);        

        foreach ($lines as $index => $line) {
            if (str_starts_with(strtolower($line), '<pre><code class="language-markdown">// filepath: ')) {
                $lines[$index] = str_replace('// Filepath: ', '<small class="filepath"><span class="sr-only">Filepath: </span>', $line) . '</small>';
            }
        }

        return implode("\n", $lines);
    }
}