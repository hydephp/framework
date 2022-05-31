<?php

namespace Tests\Feature;

use Tests\TestCase;

use Hyde\Framework\Services\Markdown\Shortcodes\DangerColoredBlockquote;
use Hyde\Framework\Services\Markdown\Shortcodes\SuccessColoredBlockquote;
use Hyde\Framework\Services\Markdown\Shortcodes\InfoColoredBlockquote;
use Hyde\Framework\Services\Markdown\Shortcodes\WarningColoredBlockquote;


/**
 * Class ColoredBlockquoteShortcodesTest.
 * 
 * @covers \Hyde\Framework\Services\Markdown\Shortcodes\AbstractColoredBlockquote
 * @covers \Hyde\Framework\Services\Markdown\Shortcodes\DangerColoredBlockquote
 * @covers \Hyde\Framework\Services\Markdown\Shortcodes\SuccessColoredBlockquote
 * @covers \Hyde\Framework\Services\Markdown\Shortcodes\InfoColoredBlockquote
 * @covers \Hyde\Framework\Services\Markdown\Shortcodes\WarningColoredBlockquote
 */
class ColoredBlockquoteShortcodesTest extends TestCase
{
    public function test_resolve_danger()
    {
        $this->assertEquals('<blockquote class="danger">foo</blockquote>',
            DangerColoredBlockquote::resolve('>danger foo'));
    }

    public function test_resolve_success()
    {
        $this->assertEquals('<blockquote class="success">foo</blockquote>',
            SuccessColoredBlockquote::resolve('>success foo'));
    }

    public function test_resolve_info()
    {
        $this->assertEquals('<blockquote class="info">foo</blockquote>',
            InfoColoredBlockquote::resolve('>info foo'));
    }

    public function test_resolve_warning()
    {
        $this->assertEquals('<blockquote class="warning">foo</blockquote>',
            WarningColoredBlockquote::resolve('>warning foo'));
    }

}
