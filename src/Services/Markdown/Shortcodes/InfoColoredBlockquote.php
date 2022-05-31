<?php

namespace Hyde\Framework\Services\Markdown\Shortcodes;

/**
 * @see \Tests\Unit\Markdown\Shortcodes\InfoColoredBlockquoteTest
 */
class InfoColoredBlockquote extends AbstractColoredBlockquote
{
    public static function signature(): string
    {
        return '>info';
    }

    protected static function expand(string $input): string
    {
        return sprintf('<blockquote class="info">%s</blockquote>',
            trim(substr($input, strlen(static::signature())), ' '));
    }
}