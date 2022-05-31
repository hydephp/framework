<?php

namespace Tests\Unit\Markdown\Shortcodes;

use Hyde\Framework\Services\Markdown\Shortcodes\WarningColoredBlockquote;
use Tests\TestCase;

/**
 * @covers \Hyde\Framework\Services\Markdown\Shortcodes\WarningColoredBlockquote
 */
class WarningColoredBlockquoteTest extends TestCase
{
    public function test_resolve()
    {
        $this->assertEquals('<blockquote class="warning">foo</blockquote>',
            WarningColoredBlockquote::resolve('>warning foo'));
    }
}
