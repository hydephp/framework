<?php

namespace Tests\Feature\Commands;

use Hyde\Framework\Hyde;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * @covers \Hyde\Framework\Commands\HydeBuildRssFeedCommand
 */
class HydeBuildRssFeedCommandTest extends TestCase
{
	public function test_rss_feed_is_not_generated_when_conditions_are_not_met()
    {
        config(['hyde.site_url' => '']);
        config(['hyde.generateRssFeed' => false]);

        unlinkIfExists(Hyde::path('_site/feed.xml'));
        $this->artisan('build:rss')
            ->assertExitCode(1);

        $this->assertFileDoesNotExist(Hyde::path('_site/feed.xml'));
    }

    public function test_rss_feed_is_generated_when_conditions_are_met()
    {
        config(['hyde.site_url' => 'https://example.com']);
        config(['hyde.generateRssFeed' => true]);

        unlinkIfExists(Hyde::path('_site/feed.xml'));
        $this->artisan('build:rss')
            ->expectsOutput('Generating RSS feed...')
            ->assertExitCode(0);

        $this->assertFileExists(Hyde::path('_site/feed.xml'));
        unlink(Hyde::path('_site/feed.xml'));
    }

    public function test_rss_filename_can_be_changed()
    {
        config(['hyde.site_url' => 'https://example.com']);
        config(['hyde.generateRssFeed' => true]);
        config(['hyde.rssFilename' => 'blog.xml']);

        unlinkIfExists(Hyde::path('_site/feed.xml'));
        unlinkIfExists(Hyde::path('_site/blog.xml'));

        $this->artisan('build:rss')
            ->expectsOutput('Generating RSS feed...')
            ->assertExitCode(0);

        $this->assertFileDoesNotExist(Hyde::path('_site/feed.xml'));
        $this->assertFileExists(Hyde::path('_site/blog.xml'));
        unlink(Hyde::path('_site/blog.xml'));
    }

    public function test_are_there_remote_images_preflight_check()
    {
        Http::fake();

        config(['hyde.site_url' => 'https://example.com']);
        config(['hyde.generateRssFeed' => true]);
        file_put_contents(Hyde::path('_posts/image.md'),  <<<'MD'
            ---
            image: https://example.org/image.png
            ---
            
            # RSS Post
            
            Foo bar
            MD
        );

        $this->artisan('build:rss')
            ->expectsOutputToContain('Heads up! There are remote images in your blog posts.')
            ->assertExitCode(0);

        unlink(Hyde::path('_site/feed.xml'));
        unlink(Hyde::path('_posts/image.md'));
    }
}
