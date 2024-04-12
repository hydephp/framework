<?php

declare(strict_types=1);

namespace Hyde\Framework\Testing\Feature;

use Hyde\Facades\Author;
use Hyde\Facades\Filesystem;
use Hyde\Framework\Actions\CreatesNewMarkdownPostFile;
use Hyde\Hyde;
use Hyde\Testing\TestCase;
use Illuminate\Support\Facades\Config;

/**
 * Test that the Author feature works in
 * conjunction with the static Post generator.
 *
 * @see StaticSiteBuilderPostModuleTest
 */
class PostsAuthorIntegrationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Config::set('hyde.authors', []);
    }

    /**
     * Baseline test to create a post without a defined author,
     * and assert that the username is displayed as is.
     *
     * Check that the author was not defined.
     * We do this by building the static site and inspecting the DOM.
     */
    public function testCreatePostWithUndefinedAuthor()
    {
        // Create a new post
        (new CreatesNewMarkdownPostFile(
            title: 'test-2dcbb2c-post-with-undefined-author',
            description: '',
            category: '',
            author: 'test_undefined_author'
        ))->save(true);

        // Check that the post was created
        $this->assertFileExists(Hyde::path('_posts/test-2dcbb2c-post-with-undefined-author.md'));

        // Build the static page
        $this->artisan('rebuild _posts/test-2dcbb2c-post-with-undefined-author.md')->assertExitCode(0);

        // Check that the file was created
        $this->assertFileExists(Hyde::path('_site/posts/test-2dcbb2c-post-with-undefined-author.html'));

        // Check that the author is rendered as is in the DOM
        $this->assertStringContainsString(
            '>test_undefined_author</span>',
            file_get_contents(Hyde::path('_site/posts/test-2dcbb2c-post-with-undefined-author.html'))
        );

        // Remove the test files
        Filesystem::unlink('_posts/test-2dcbb2c-post-with-undefined-author.md');
        Filesystem::unlink('_site/posts/test-2dcbb2c-post-with-undefined-author.html');
    }

    /**
     * Test that a defined author has its name injected into the DOM.
     */
    public function testCreatePostWithDefinedAuthorWithName()
    {
        // Create a new post
        (new CreatesNewMarkdownPostFile(
            title: 'test-2dcbb2c-post-with-defined-author-with-name',
            description: '',
            category: '',
            author: 'test_named_author'
        ))->save(true);

        // Check that the post was created
        $this->assertFileExists(Hyde::path('_posts/test-2dcbb2c-post-with-defined-author-with-name.md'));

        Config::set('hyde.authors', [
            Author::create('test_named_author', 'Test Author', null),
        ]);

        // Check that the post was created
        $this->assertFileExists(Hyde::path('_posts/test-2dcbb2c-post-with-defined-author-with-name.md'));
        // Build the static page
        $this->artisan('rebuild _posts/test-2dcbb2c-post-with-defined-author-with-name.md')->assertExitCode(0);
        // Check that the file was created
        $this->assertFileExists(Hyde::path('_site/posts/test-2dcbb2c-post-with-defined-author-with-name.html'));

        // Check that the author is contains the set name in the DOM
        $this->assertStringContainsString(
            '<span itemprop="name" aria-label="The author\'s name" title=@test_named_author>Test Author</span>',
            file_get_contents(Hyde::path('_site/posts/test-2dcbb2c-post-with-defined-author-with-name.html'))
        );

        // Remove the test files
        Filesystem::unlink('_posts/test-2dcbb2c-post-with-defined-author-with-name.md');
        Filesystem::unlink('_site/posts/test-2dcbb2c-post-with-defined-author-with-name.html');
    }

    /**
     * Test that a defined author with website has its site linked.
     */
    public function testCreatePostWithDefinedAuthorWithWebsite()
    {
        // Create a new post
        (new CreatesNewMarkdownPostFile(
            title: 'test-2dcbb2c-post-with-defined-author-with-name',
            description: '',
            category: '',
            author: 'test_author_with_website'
        ))->save(true);

        // Check that the post was created
        $this->assertFileExists(Hyde::path('_posts/test-2dcbb2c-post-with-defined-author-with-name.md'));

        Config::set('hyde.authors', [
            Author::create('test_author_with_website', 'Test Author', 'https://example.org'),
        ]);

        // Check that the post was created
        $this->assertFileExists(Hyde::path('_posts/test-2dcbb2c-post-with-defined-author-with-name.md'));
        // Build the static page
        $this->artisan('rebuild _posts/test-2dcbb2c-post-with-defined-author-with-name.md')->assertExitCode(0);
        // Check that the file was created
        $this->assertFileExists(Hyde::path('_site/posts/test-2dcbb2c-post-with-defined-author-with-name.html'));

        // Check that the author is contains the set name in the DOM
        $this->assertStringContainsString(
            '<span itemprop="name" aria-label="The author\'s name" title=@test_author_with_website>Test Author</span>',
            file_get_contents(Hyde::path('_site/posts/test-2dcbb2c-post-with-defined-author-with-name.html'))
        );

        // Check that the author is contains the set website in the DOM
        $this->assertStringContainsString(
            '<a href="https://example.org" rel="author" itemprop="url" aria-label="The author\'s website">',
            file_get_contents(Hyde::path('_site/posts/test-2dcbb2c-post-with-defined-author-with-name.html'))
        );

        // Remove the test files
        Filesystem::unlink('_posts/test-2dcbb2c-post-with-defined-author-with-name.md');
        Filesystem::unlink('_site/posts/test-2dcbb2c-post-with-defined-author-with-name.html');
    }
}
