<?php

namespace Hyde\Testing\Framework\Unit;

use Hyde\Framework\Hyde;
use Hyde\Testing\TestCase;
use Illuminate\Support\Facades\Artisan;

class BlogPostFrontMatterIsOptionalTest extends TestCase
{
    public function test_blog_post_can_be_created_without_front_matter()
    {
        file_put_contents(Hyde::path('_posts/test-post.md'), '# My New Post');

        Artisan::call('rebuild _posts/test-post.md');

        $this->assertFileExists(Hyde::path('_site/posts/test-post.html'));

        unlink(Hyde::path('_posts/test-post.md'));
        unlink(Hyde::path('_site/posts/test-post.html'));
    }

    public function test_blog_post_feed_can_be_rendered_when_post_has_no_front_matter()
    {
        file_put_contents(Hyde::path('_posts/test-post.md'), '# My New Post');

        // Create a temporary page to test the feed
        file_put_contents(Hyde::path('_pages/feed-test.blade.php'),
            '@foreach(\Hyde\Framework\Models\MarkdownPost::getLatestPosts() as $post)
				@include(\'hyde::components.article-excerpt\')
			@endforeach'
        );

        Artisan::call('rebuild _pages/feed-test.blade.php');

        $this->assertFileExists(Hyde::path('_site/feed-test.html'));

        unlink(Hyde::path('_posts/test-post.md'));
        unlink(Hyde::path('_pages/feed-test.blade.php'));
        unlink(Hyde::path('_site/feed-test.html'));
    }
}
