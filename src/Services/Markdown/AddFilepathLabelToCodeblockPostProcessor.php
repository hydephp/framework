<?php

namespace Hyde\Framework\Services\Markdown;

/**
 * @todo Add config option to enable/disable this processor
 */
class AddFilepathLabelToCodeblockPostProcessor
{
    public static function process(string $html): string
    {
        $torchlightKey = '<!-- Syntax highlighted by torchlight.dev -->';
        $template = '<small class="filepath"><span class="sr-only">Filepath: </span>%s</small>';

        if (str_contains($html, $torchlightKey)) {

            $lines = explode("\n", $html);

            foreach ($lines as $index => $line) {
                if (str_starts_with($line, '<!-- HYDE[Filepath]')) {
                    $path = trim(
                        str_replace('-->', '', str_replace(
                            '<!-- HYDE[Filepath]', '', $line
                        ))
                    );
                    unset($lines[$index]);
                    $codeBlockLine = $index + 1;
                    $lines[$codeBlockLine] = str_replace(
                        $torchlightKey,
                        $torchlightKey . sprintf($template, $path, $lines[$codeBlockLine]),
                        $lines[$codeBlockLine]
                        );
                }
            }

            return implode("\n", $lines);
        }

        $lines = explode("\n", $html);

        foreach ($lines as $index => $line) {
            if (str_starts_with($line, '<!-- HYDE[Filepath]')) {
                $path = trim(
                    str_replace('-->', '', str_replace(
                        '<!-- HYDE[Filepath]', '', $line
                    ))
                );
                unset($lines[$index]);

                $codeBlockLine = $index + 1;

                $label = sprintf($template, $path, $lines[$codeBlockLine]);

                // Insert the label after the '<pre><code class="language-*">' using regex
                $lines[$codeBlockLine] = preg_replace(
                    '/<pre><code class="language-(.*?)">/',
                    '<pre><code class="language-$1">' . $label,
                    $lines[$codeBlockLine]
                );
            }
        }

        return implode("\n", $lines);

    }

    public static function preprocess(string $markdown): string
    {
        $lines = explode("\n", $markdown);

        foreach ($lines as $index => $line) {
            if (static::lineMatchesPattern($line) && ! str_contains($line, '// HYDE! {"shortcodes": false} HYDE! //')) {
                // Add the meta-block two lines before the pattern, placing it just above the code block.
                // This prevents the meta-block from interfering with other processes.
                $lines[$index - 2] .= "\n".'<!-- HYDE[Filepath]'.trim(str_replace(static::$patterns, '', $line)).' -->'; 

                // Remove the original comment lines
                unset($lines[$index]);
                // Only unset the next line if it's empty
                if (trim($lines[$index + 1]) === '') {
                    unset($lines[$index + 1]);
                }
            }
        }

        return implode("\n", $lines);
    }

    protected static array $patterns = [
        '// filepath: ',
        '// Filepath: ',
        '# filepath: ',
        '# Filepath: ',
        '// filepath ',
        '// Filepath ',
        '# filepath ',
        '# Filepath ',
    ];

    protected static function lineMatchesPattern(string $line): bool
    {
        foreach (static::$patterns as $pattern) {
            if (str_starts_with($line, $pattern)) {
                return true;
            }
        }

        return false;
    }
}