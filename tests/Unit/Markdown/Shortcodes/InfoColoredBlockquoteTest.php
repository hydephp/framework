<?php

namespace Tests\Unit\Markdown\Shortcodes;

use Hyde\Framework\Services\Markdown\Shortcodes\InfoColoredBlockquote;
use Tests\TestCase;

/**
 * @covers \Hyde\Framework\Services\Markdown\Shortcodes\InfoColoredBlockquote
 */
class InfoColoredBlockquoteTest extends TestCase
{
    public function test_resolve()
    {
        $this->assertEquals('<blockquote class="info">foo</blockquote>',
            InfoColoredBlockquote::resolve('>info foo'));
    }
}
