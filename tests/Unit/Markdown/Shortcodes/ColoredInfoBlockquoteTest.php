<?php

namespace Tests\Unit\Markdown\Shortcodes;

use Hyde\Framework\Services\Markdown\Shortcodes\ColoredInfoBlockquote;
use Tests\TestCase;

/**
 * @covers \Hyde\Framework\Services\Markdown\Shortcodes\ColoredInfoBlockquote
 */
class ColoredInfoBlockquoteTest extends TestCase
{
    public function test_signature()
    {
        $this->assertEquals('>info', ColoredInfoBlockquote::signature());
    }

    public function test_resolve()
    {
        $this->assertEquals('<blockquote class="info">foo</blockquote>',
            ColoredInfoBlockquote::resolve('>info foo'));
        $this->assertEquals('>foo', ColoredInfoBlockquote::resolve('>foo'));
    }
}
