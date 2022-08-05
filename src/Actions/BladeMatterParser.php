<?php

namespace Hyde\Framework\Actions;

use Hyde\Framework\Hyde;

/**
 * Parse the front matter in a Blade file.
 *
 * Accepts a string to make it easier to mock when testing.
 *
 * @see \Hyde\Framework\Testing\Feature\BladeMatterParserTest
 *
 * === DOCUMENTATION (draft) ===
 *
 * ## Front Matter in Markdown
 *
 * HydePHP uses a special syntax called BladeMatter that allows you to define variables in a Blade file,
 * and have Hyde statically parse them into the front matter of the page model. This allows metadata
 * in your Blade pages to be used when Hyde generates dynamic data like page titles and SEO tags.
 *
 * ### Syntax
 *
 * Any line following the syntax below will be added to the parsed page object's front matter.
 *
 * @example `@php($title = 'BladeMatter Test')`
 * This would then be parsed into the following array in the page model: ['title' => 'BladeMatter Test']
 *
 * ### Limitations
 * Each directive must be on its own line, and start with `@php($.`. Arrays are currently not supported.
 */
class BladeMatterParser
{
    protected string $contents;
    protected array $matter;

    /** The directive signature used to determine if a line should be parsed. */
    protected const SEARCH = '@php($';

    public static function parseFile(string $localFilePath): array
    {
        return static::parseString(file_get_contents(Hyde::path($localFilePath)));
    }

    public static function parseString(string $contents): array
    {
        return (new static($contents))->parse()->get();
    }

    public function __construct(string $contents)
    {
        $this->contents = $contents;
    }

    public function get(): array
    {
        return $this->matter;
    }

    public function parse(): static
    {
        $this->matter = [];

        $lines = explode("\n", $this->contents);

        foreach ($lines as $line) {
            if (static::lineMatchesFrontMatter($line)) {
                $this->matter[static::extractKey($line)] = static::normalizeValue(static::extractValue($line));
            }
        }

        return $this;
    }

    /** @internal */
    public static function lineMatchesFrontMatter(string $line): bool
    {
        return str_starts_with($line, static::SEARCH);
    }

    /** @internal */
    public static function extractKey(string $line): string
    {
        // Remove search prefix
        $key = substr($line, strlen(static::SEARCH));

        // Remove everything after the first equals sign
        $key = substr($key, 0, strpos($key, '='));

        // Return trimmed line
        return trim($key);
    }

    /** @internal */
    public static function extractValue(string $line): string
    {
        // Trim any trailing spaces and newlines
        $key = trim($line);

        // Remove everything before the first equals sign
        $key = substr($key, strpos($key, '=') + 1);

        // Remove closing parenthesis
        $key = substr($key, 0, strlen($key) - 1);

        // Remove any quotes so we can normalize the value
        $key = trim($key, ' "\'');

        // Return trimmed line
        return trim($key);
    }

    /** @internal */
    public static function normalizeValue($value): mixed
    {
        if ($value === 'null') {
            return null;
        }

        // This will cast integers, floats, and booleans to their respective types
        // Still working on a way to handle arrays and objects
        return json_decode($value) ?? $value;
    }
}
