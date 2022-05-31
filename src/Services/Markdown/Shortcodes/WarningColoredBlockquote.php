<?php

namespace Hyde\Framework\Services\Markdown\Shortcodes;

/**
 * @see \Tests\Unit\Markdown\Shortcodes\WarningColoredBlockquoteTest
 */
class WarningColoredBlockquote extends AbstractColoredBlockquote
{
    protected static string $signature = '>warning';

    protected static function expand(string $input): string
    {
        return sprintf('<blockquote class="warning">%s</blockquote>',
            trim(substr($input, strlen(static::signature())), ' '));
    }
}