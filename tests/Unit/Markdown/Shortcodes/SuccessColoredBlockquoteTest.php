<?php

namespace Tests\Unit\Markdown\Shortcodes;

use Hyde\Framework\Services\Markdown\Shortcodes\SuccessColoredBlockquote;
use Tests\TestCase;

/**
 * @covers \Hyde\Framework\Services\Markdown\Shortcodes\SuccessColoredBlockquote
 */
class SuccessColoredBlockquoteTest extends TestCase
{
    public function test_resolve()
    {
        $this->assertEquals('<blockquote class="success">foo</blockquote>',
            SuccessColoredBlockquote::resolve('>success foo'));
    }
}
