<?php

namespace Hyde\Framework\Services\Markdown\Shortcodes;

/**
 * @see \Tests\Unit\Markdown\Shortcodes\DangerColoredBlockquoteTest
 */
class DangerColoredBlockquote extends AbstractColoredBlockquote
{
    protected static string $signature = '>danger';

    protected static function expand(string $input): string
    {
        return sprintf('<blockquote class="danger">%s</blockquote>',
            trim(substr($input, strlen(static::signature())), ' '));
    }
}