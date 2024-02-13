<?php

declare(strict_types=1);

namespace Hyde\Framework\Testing\Unit;

use Hyde\Facades\Filesystem;
use Hyde\Hyde;
use Hyde\Pages\MarkdownPost;
use Hyde\Testing\TestCase;
use Illuminate\Support\Collection;

/**
 * @see \Hyde\Pages\MarkdownPost::latest()
 */
class GetLatestMarkdownPostsTest extends TestCase
{
    public function testMarkdownPageGetLatestHelperReturnsSortedMarkdownPageCollection()
    {
        file_put_contents(Hyde::path('_posts/new.md'), "---\ndate: '2022-01-01 12:00'\n---\n");
        file_put_contents(Hyde::path('_posts/old.md'), "---\ndate: '2021-01-01 12:00'\n---\n");

        $collection = MarkdownPost::getLatestPosts();
        $this->assertCount(2, $collection);
        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertContainsOnlyInstancesOf(MarkdownPost::class, $collection);

        $this->assertEquals('new', $collection->first()->identifier);
        $this->assertEquals('old', $collection->last()->identifier);

        Filesystem::unlink('_posts/new.md');
        Filesystem::unlink('_posts/old.md');
    }
}
