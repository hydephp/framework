<?php

namespace Tests\Unit\Markdown\Shortcodes;

use Hyde\Framework\Services\Markdown\Shortcodes\DangerColoredBlockquote;
use Tests\TestCase;

/**
 * @covers \Hyde\Framework\Services\Markdown\Shortcodes\DangerColoredBlockquote
 */
class DangerColoredBlockquoteTest extends TestCase
{
    public function test_resolve()
    {
        $this->assertEquals('<blockquote class="danger">foo</blockquote>',
            DangerColoredBlockquote::resolve('>danger foo'));
    }
}
