<?php

namespace Hyde\Framework\Services\Markdown\Shortcodes;

use Hyde\Framework\Contracts\MarkdownShortcodeContract;

/**
 * @see \Tests\Unit\Markdown\Shortcodes\DangerColoredBlockquoteTest
 */
class DangerColoredBlockquote implements MarkdownShortcodeContract
{
    public static function signature(): string
    {
        return '>danger';
    }

    public static function resolve(string $input): string
    {
        return str_starts_with($input, static::signature())
            ? self::expand($input)
            : $input;
    }

    protected static function expand(string $input): string
    {
        return sprintf('<blockquote class="danger">%s</blockquote>',
            trim(substr($input, strlen(static::signature())), ' '));
    }
}