<?php

namespace Hyde\Framework\Actions;

/**
 * Convert an array into YAML Front Matter.
 */
class ConvertsArrayToFrontMatter
{
    /**
     * Execute the action.
     *
     * @param  array  $array
     * @return string $yaml front matter
     */
    public function execute(array $array): string
    {
        // Initialize the array
        $yaml = [];

        // Set the first line to the opening starting block
        $yaml[] = '---';

        // For each line, add the key-value pair as YAML
        foreach ($array as $key => $value) {
            if (trim($value) !== '' && $value !== null) {
                $yaml[] = "$key: $value";
            }
        }

        // Set the closing block
        $yaml[] = '---';

        // Add an extra line
        $yaml[] = '';

        // Return the array imploded into a string with newline characters
        return implode("\n", $yaml);
    }
}
