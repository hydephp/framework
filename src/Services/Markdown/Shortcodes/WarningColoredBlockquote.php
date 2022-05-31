<?php

namespace Hyde\Framework\Services\Markdown\Shortcodes;

use Hyde\Framework\Contracts\MarkdownShortcodeContract;

/**
 * @see \Tests\Unit\Markdown\Shortcodes\WarningColoredBlockquoteTest
 */
class WarningColoredBlockquote implements MarkdownShortcodeContract
{
    public static function signature(): string
    {
        return '>warning';
    }

    public static function resolve(string $input): string
    {
        return str_starts_with($input, static::signature())
            ? self::expand($input)
            : $input;
    }

    protected static function expand(string $input): string
    {
        return sprintf('<blockquote class="warning">%s</blockquote>',
            trim(substr($input, strlen(static::signature())), ' '));
    }
}