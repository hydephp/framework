<?php

namespace Hyde\Testing\Framework\Feature;

use Hyde\Framework\Services\Markdown\Shortcodes\AbstractColoredBlockquote;
use Hyde\Testing\TestCase;

/**
 * Class ColoredBlockquoteShortcodesTest.
 *
 * @covers \Hyde\Framework\Services\Markdown\Shortcodes\AbstractColoredBlockquote
 */
class ColoredBlockquoteShortcodesTest extends TestCase
{
    public function test_resolve_method()
    {
        $this->assertEquals('<blockquote class="color">foo</blockquote>',
            AbstractColoredBlockquote::resolve('>color foo'));
    }

    public function test_get_method()
    {
        $this->assertCount(4, AbstractColoredBlockquote::get());
        $this->assertContainsOnlyInstancesOf(AbstractColoredBlockquote::class,
            AbstractColoredBlockquote::get());
    }
}
