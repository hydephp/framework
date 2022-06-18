<?php

namespace Hyde\Testing\Framework\Feature\Services\Markdown;

use Hyde\Framework\Contracts\MarkdownShortcodeContract;
use Hyde\Framework\Services\Markdown\ShortcodeProcessor;
use Hyde\Testing\TestCase;

/**
 * @covers \Hyde\Framework\Services\Markdown\ShortcodeProcessor
 */
class ShortcodeProcessorTest extends TestCase
{
    // Test constructor discovers default shortcodes
    public function test_constructor_discovers_default_shortcodes()
    {
        $shortcodes = (new ShortcodeProcessor('foo'))->shortcodes;

        $this->assertCount(4, $shortcodes);
        $this->assertContainsOnlyInstancesOf(MarkdownShortcodeContract::class, $shortcodes);
    }

    // Test discovered shortcodes are used to process input
    public function test_discovered_shortcodes_are_used_to_process_input()
    {
        $processor = new ShortcodeProcessor('>info foo');

        $this->assertEquals('<blockquote class="info">foo</blockquote>',
            $processor->run());
    }

    // Test string not matching shortcode is not modified
    public function test_string_without_shortcode_is_not_modified()
    {
        $processor = new ShortcodeProcessor('foo');

        $this->assertEquals('foo', $processor->run());
    }

    // Test the static process() shorthand method
    public function test_process_static_shorthand()
    {
        $this->assertEquals('<blockquote class="info">foo</blockquote>',
            ShortcodeProcessor::process('>info foo'));
    }

    // Test shortcodes can be added to the processor
    public function test_shortcodes_can_be_added_to_processor()
    {
        $processor = new ShortcodeProcessor('foo');

        $processor->addShortcode(new class implements MarkdownShortcodeContract
        {
            public static function signature(): string
            {
                return 'foo';
            }

            public static function resolve(string $input): string
            {
                return 'bar';
            }
        });

        $this->assertArrayHasKey('foo', $processor->shortcodes);
        $this->assertEquals('bar', $processor->run());
    }
}
