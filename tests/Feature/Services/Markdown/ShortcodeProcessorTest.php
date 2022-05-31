<?php

namespace Tests\Feature\Services\Markdown;

use Hyde\Framework\Contracts\MarkdownShortcodeContract;
use Hyde\Framework\Services\Markdown\ShortcodeProcessor;
use Hyde\Framework\Services\Markdown\Shortcodes\ColoredInfoBlockquote;
use Tests\TestCase;

/**
 * @covers \Hyde\Framework\Services\Markdown\ShortcodeProcessor
 */
class ShortcodeProcessorTest extends TestCase
{
    // Test constructor discovers default shortcodes
    public function test_constructor_discovers_default_shortcodes()
    {
        $shortcodes = (new ShortcodeProcessor('foo'))->shortcodes;

        $this->assertCount(1, $shortcodes);
        $this->assertContains(ColoredInfoBlockquote::class, $shortcodes);
        $this->assertInstanceOf(MarkdownShortcodeContract::class,
            new $shortcodes[ColoredInfoBlockquote::signature()]);
    }
}
