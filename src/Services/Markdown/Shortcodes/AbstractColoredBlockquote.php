<?php

namespace Hyde\Framework\Services\Markdown\Shortcodes;

use Hyde\Framework\Contracts\MarkdownShortcodeContract;

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
}