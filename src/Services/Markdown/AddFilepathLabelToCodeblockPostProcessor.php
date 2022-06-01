<?php

namespace Hyde\Framework\Services\Markdown;

class AddFilepathLabelToCodeblockPostProcessor
{
    /**
     * DOMDocument Proof of Concept
     *
     * @todo add ext-dom suggestion to composer.json
     */
    public static function process(string $html): string
    {
        if (! extension_loaded('dom')) {
            return $html;
        }

        // Find all the code blocks
        $dom = new \DOMDocument();
        $dom->loadHTML($html);
        $xpath = new \DOMXPath($dom);
        // Get query matching <pre><code>
        $query = '//pre/code';
        $codeBlocks = $xpath->query($query);

        // Add the filepath label to each code block
        foreach ($codeBlocks as $codeBlock) {
            // Check if element contains the `// Filepath:` string
            if (! str_contains(strtolower($codeBlock->textContent), '// filepath:')) {
                continue;
            }

            // Get the filepath which is everything after `// Filepath:` on the first line of the code block
            $filepath = trim(explode("\n", $codeBlock->textContent)[0]);
            $filepath = substr($filepath, strpos($filepath, ':') + 1);

            // Remove the first line of the code block text
            $text = explode("\n", $codeBlock->textContent);
            array_shift($text);
            array_shift($text);
            $codeBlock->textContent = implode("\n", $text);

            // Create the filepath label `<small>$filepath<small>` element
            $filepathLabel = $dom->createElement('small', $filepath);
            // Add the class `filepath` to the filepath label
            $filepathLabel->setAttribute('class', 'filepath');

            // Prepend the filepath label to the first child of the code block
            $codeBlock->insertBefore($filepathLabel, $codeBlock->firstChild);
        }

        return $dom->saveHTML();
    }
}