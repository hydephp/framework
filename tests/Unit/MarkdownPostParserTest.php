<?php

namespace Hyde\Framework\Testing\Unit;

use Hyde\Framework\Hyde;
use Hyde\Framework\Models\FrontMatter;
use Hyde\Framework\Models\Markdown;
use Hyde\Framework\Models\Pages\MarkdownPost;
use Hyde\Testing\TestCase;

/**
 * @see \Hyde\Framework\Testing\Feature\Commands\StaticSiteBuilderPostModuleTest for the compiler test.
 */
class MarkdownPostParserTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        createTestPost();
    }

    protected function tearDown(): void
    {
        unlink(Hyde::path('_posts/test-post.md'));

        parent::tearDown();
    }

    public function test_can_parse_markdown_file()
    {
        $post = MarkdownPost::parse('test-post');
        $this->assertInstanceOf(MarkdownPost::class, $post);
        $this->assertCount(3, ($post->matter->toArray()));
        $this->assertInstanceOf(FrontMatter::class, $post->matter);
        $this->assertInstanceOf(Markdown::class, $post->markdown);
        $this->assertIsString($post->markdown->body);
        $this->assertIsString($post->identifier);
        $this->assertTrue(strlen($post->markdown) > 32);
        $this->assertTrue(strlen($post->identifier) > 8);
    }

    public function test_parsed_markdown_post_contains_valid_front_matter()
    {
        $post = MarkdownPost::parse('test-post');
        $this->assertEquals('My New Post', $post->get('title'));
        $this->assertEquals('Mr. Hyde', $post->get('author'));
        $this->assertEquals('blog', $post->get('category'));
    }
}
