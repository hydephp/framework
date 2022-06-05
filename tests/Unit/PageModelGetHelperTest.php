<?php

namespace Tests\Unit;

use Hyde\Framework\Hyde;
use Hyde\Framework\Models\MarkdownPost;
use Illuminate\Support\Collection;
use Tests\TestCase;

/**
 * @covers \Hyde\Framework\Contracts\PageContract::all
 */
class PageModelGetHelperTest extends TestCase
{
    /**
     * @covers \Hyde\Framework\Models\MarkdownPost::all
     */
    public function test_markdown_post_get_helper_returns_markdown_post_collection()
    {
        touch(Hyde::path('_posts/test-post.md'));

        $collection = MarkdownPost::all();
        $this->assertCount(1, $collection);
        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertContainsOnlyInstancesOf(MarkdownPost::class, $collection);

        unlink(Hyde::path('_posts/test-post.md'));
    }
}
