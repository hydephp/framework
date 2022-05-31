<?php

namespace Hyde\Framework\Services\Markdown\Shortcodes;

use Hyde\Framework\Contracts\MarkdownShortcodeContract;

abstract class AbstractColoredBlockquote implements MarkdownShortcodeContract
{
	public static function resolve(string $input): string
	{
		return str_starts_with($input, static::signature())
			? static::expand($input)
			: $input;
	}
}