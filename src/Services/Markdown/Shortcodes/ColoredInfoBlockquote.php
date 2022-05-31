<?php

namespace Hyde\Framework\Services\Markdown\Shortcodes;

use Hyde\Framework\Contracts\MarkdownShortcodeContract;

/**
 * @see \Tests\Unit\Markdown\Shortcodes\ColoredInfoBlockquoteTest
 */
class ColoredInfoBlockquote implements MarkdownShortcodeContract
{
    public static function signature(): string
    {
        return '>info';
    }

    public static function resolve(string $input): string
    {
        return str_starts_with($input, static::signature())
            ? self::expand($input)
            : $input;
    }

    protected static function expand(string $input): string
    {
        return '<blockquote class="info">' . trim(substr($input, strlen(static::signature())), ' ') . '</blockquote>';
    }
}