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
        return (new static($html))->run();
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
            if ($this->lineMatchesPattern($firstLine)) {
                // Get the filepath
                $filepath = trim(str_replace(self::$patterns, '', $firstLine));

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

    protected function lineMatchesPattern(string $line): bool
    {
        foreach (static::$patterns as $pattern) {
            if (str_starts_with($line, $pattern)) {
                return true;
            }
        }

        return false;
    }
}