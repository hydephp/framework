<?php

namespace Hyde\Framework\Services\Markdown;

/**
 * DOMDocument Proof of Concept
 *
 * @note This does not work with Torchlight highlighted code blocks.
 *
 * @todo Add ext-dom suggestion to composer.json
 * @todo Add config option to enable/disable this processor
 */
class AddFilepathLabelToCodeblockPostProcessor
{
    public static function process(string $html): string
    {
        $torchlightKey = '<!-- Syntax highlighted by torchlight.dev -->';
        $template = '<small class="filepath"><span class="sr-only">Filepath: </span>%s</small>';

        if (! str_contains($html, $torchlightKey)) {
            return (new static($html))->run();
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
                $lines[$codeBlockLine] = str_replace(
                    $torchlightKey,
                    $torchlightKey . sprintf($template, $path, $lines[$codeBlockLine]),
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
            if (static::lineMatchesPattern($line)) {
                // Add the meta-block two lines before the pattern, placing it just above the code block.
                // This prevents the meta-block from interfering with other processes.
                $lines[$index - 2] .= "\n".'<!-- HYDE[Filepath]'.trim(str_replace(static::$patterns, '', $line)).' -->'; 

                // Remove the original comment lines
                unset($lines[$index]);
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

    protected string $html;

    public function __construct(string $html) {
        $this->html = $html;

    }

    public function run(): string
    {
        if (! extension_loaded('dom')) {
            return $this->html;
        }

        // Find all the code blocks
        $dom = new \DOMDocument();
        $dom->loadHTML($this->html);
        $xpath = new \DOMXPath($dom);
        // Get query matching <pre><code>
        $query = '//pre/code';
        $codeBlocks = $xpath->query($query);

        // Add the filepath label to each code block
        foreach ($codeBlocks as $codeBlock) {
            // Get the first line (everything before the first newline in $codeBlock->textContent)
            $firstLine = strtok($codeBlock->textContent, "\n");
            
            // Check if it matches any of the patterns
            if (static::lineMatchesPattern($firstLine)) {
                // Get the filepath
                $filepath = trim(str_replace(static::$patterns, '', $firstLine));

                // Remove the first line of the code block text
                $codeBlock->textContent = substr($codeBlock->textContent,
                    strpos($codeBlock->textContent, "\n") + 2);

                // Create the filepath label `<small>$filepath<small>` element
                $filepathLabel = $dom->createElement('small', $filepath);
                // Add the class `filepath` to the filepath label
                $filepathLabel->setAttribute('class', 'filepath');
                $filepathLabel->setAttribute('title', 'Filepath');
                // Add screen reader text
                $screenReaderText = $dom->createElement('span', 'Filepath: ');
                $screenReaderText->setAttribute('class', 'sr-only');
                $filepathLabel->insertBefore($screenReaderText, $filepathLabel->firstChild);

                // Prepend the filepath label to the first child of the code block
                $codeBlock->insertBefore($filepathLabel, $codeBlock->firstChild);
            }
        }

        return $dom->saveHTML();
    }

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