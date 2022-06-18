<?php

namespace Hyde\Testing\Framework\Unit;

use Hyde\Framework\Hyde;
use Hyde\Framework\Models\MarkdownPost;
use Hyde\Testing\TestCase;
use Illuminate\Support\Collection;

/**
 * @deprecated as parent method is deprecated
 * @covers \Hyde\Framework\Hyde::getLatestPosts
 */
class HydeGetLatestPostsHelperTest extends TestCase
{
    public function test_get_latest_posts_helper_returns_collection()
    {
        $this->assertInstanceOf(Collection::class, Hyde::getLatestPosts());
    }

    public function test_get_latest_posts_helper_returns_collection_with_posts()
    {
        file_put_contents(MarkdownPost::$sourceDirectory.'/foo.md', '');

        $collection = Hyde::getLatestPosts();
        $this->assertTrue($collection->isNotEmpty());
        $this->assertInstanceOf(MarkdownPost::class, $collection->first());

        unlink(MarkdownPost::$sourceDirectory.'/foo.md');
    }
}
