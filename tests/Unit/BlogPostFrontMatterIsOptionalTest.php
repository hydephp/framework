<?php

namespace Hyde\Testing\Framework\Unit;

use Hyde\Framework\Hyde;
use Hyde\Testing\TestCase;
use Illuminate\Support\Facades\Artisan;

class BlogPostFrontMatterIsOptionalTest extends TestCase
{
    // Test blog posts can be created without having any front matter
    public function testBlogPostCanBeCreatedWithoutFrontMatter()
    {
        file_put_contents(Hyde::path('_posts/test-post.md'), '# My New Post');

        Artisan::call('rebuild _posts/test-post.md');

        $this->assertFileExists(Hyde::path('_site/posts/test-post.html'));

        unlink(Hyde::path('_posts/test-post.md'));
        unlink(Hyde::path('_site/posts/test-post.html'));
    }

    // Test blog post feed can be rendered when having post without front matter
    public function testBlogPostFeedCanBeRenderedWhenPostHasNoFrontMatter()
    {
        file_put_contents(Hyde::path('_posts/test-post.md'), '# My New Post');

        // Create a temporary page to test the feed
        file_put_contents(Hyde::path('_pages/feed-test.blade.php'),
            '@foreach(Hyde::getLatestPosts() as $post)
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
