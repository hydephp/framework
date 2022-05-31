<?php

namespace Hyde\Framework\Services\Markdown\Shortcodes;

/**
 * @see \Tests\Unit\Markdown\Shortcodes\SuccessColoredBlockquoteTest
 */
class SuccessColoredBlockquote extends AbstractColoredBlockquote
{
    public static function signature(): string
    {
        return '>success';
    }

    protected static function expand(string $input): string
    {
        return sprintf('<blockquote class="success">%s</blockquote>',
            trim(substr($input, strlen(static::signature())), ' '));
    }
}