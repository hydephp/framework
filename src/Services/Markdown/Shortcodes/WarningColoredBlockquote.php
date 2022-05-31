<?php

namespace Hyde\Framework\Services\Markdown\Shortcodes;

/**
 * @see \Tests\Unit\Markdown\Shortcodes\WarningColoredBlockquoteTest
 */
class WarningColoredBlockquote extends AbstractColoredBlockquote
{
    public static function signature(): string
    {
        return '>warning';
    }

    protected static function expand(string $input): string
    {
        return sprintf('<blockquote class="warning">%s</blockquote>',
            trim(substr($input, strlen(static::signature())), ' '));
    }
}