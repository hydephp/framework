<?php

namespace Hyde\Framework\Modules\Markdown\Shortcodes;

use Hyde\Framework\Contracts\MarkdownShortcodeContract;

/**
 * @see \Hyde\Framework\Testing\Feature\ColoredBlockquoteShortcodesTest
 */
abstract class AbstractColoredBlockquote implements MarkdownShortcodeContract
{
    protected static string $signature = '>color';

    public static function signature(): string
    {
        return static::$signature;
    }

    public static function resolve(string $input): string
    {
        return str_starts_with($input, static::signature())
            ? static::expand($input)
            : $input;
    }

    protected static function expand(string $input): string
    {
        return sprintf(
            '<blockquote class="%s">%s</blockquote>',
            static::getClassNameFromSignature(static::signature()),
            trim(substr($input, strlen(static::signature())), ' ')
        );
    }

    protected static function getClassNameFromSignature(string $signature): string
    {
        return str_replace('>', '', $signature);
    }

    /**
     * @return array (AbstractColoredBlockquote)[]
     */
    public static function get(): array
    {
        return [
            new class extends AbstractColoredBlockquote
            {
                protected static string $signature = '>danger';
            },
            new class extends AbstractColoredBlockquote
            {
                protected static string $signature = '>info';
            },
            new class extends AbstractColoredBlockquote
            {
                protected static string $signature = '>success';
            },
            new class extends AbstractColoredBlockquote
            {
                protected static string $signature = '>warning';
            },
        ];
    }
}
