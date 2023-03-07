<?php

declare(strict_types=1);

namespace Hyde\Framework\Testing\Feature;

use Hyde\Framework\Features\Blogging\Models\FeaturedImage;
use Hyde\Framework\Features\Blogging\Models\PostAuthor;
use Hyde\Markdown\Models\FrontMatter;
use Hyde\Pages\MarkdownPost;
use Hyde\Support\Models\DateString;
use Hyde\Testing\TestCase;

/**
 * @covers \Hyde\Pages\MarkdownPost
 * @covers \Hyde\Framework\Factories\Concerns\HasFactory
 */
class MarkdownPostTest extends TestCase
{
    public function test_constructor_can_create_a_new_author_instance_from_username_string()
    {
        $post = new MarkdownPost(matter: FrontMatter::fromArray([
            'author' => 'John Doe',
        ]));

        $this->assertInstanceOf(PostAuthor::class, $post->author);
        $this->assertEquals('John Doe', $post->author->username);
        $this->assertNull($post->author->name);
        $this->assertNull($post->author->website);
    }

    public function test_constructor_can_create_a_new_author_instance_from_user_array()
    {
        $post = new MarkdownPost(matter: FrontMatter::fromArray([
            'author' => [
                'username' => 'john_doe',
                'name' => 'John Doe',
                'website' => 'https://example.com',
            ],
        ]));

        $this->assertInstanceOf(PostAuthor::class, $post->author);
        $this->assertEquals('john_doe', $post->author->username);
        $this->assertEquals('John Doe', $post->author->name);
        $this->assertEquals('https://example.com', $post->author->website);
    }

    public function test_constructor_can_create_a_new_image_instance_from_a_string()
    {
        $post = new MarkdownPost(matter: FrontMatter::fromArray([
            'image' => 'https://example.com/image.jpg',
        ]));

        $this->assertInstanceOf(FeaturedImage::class, $post->image);
        $this->assertEquals('https://example.com/image.jpg', $post->image->getSource());
    }

    public function test_constructor_can_create_a_new_image_instance_from_an_array()
    {
        $post = new MarkdownPost(matter: FrontMatter::fromArray([
            'image' => [
                'source' => 'https://example.com/image.jpg',
            ],
        ]));

        $this->assertInstanceOf(FeaturedImage::class, $post->image);
        $this->assertEquals('https://example.com/image.jpg', $post->image->getSource());
    }

    public function test_constructor_can_create_a_new_date_string_instance_from_matter()
    {
        $post = new MarkdownPost(matter: FrontMatter::fromArray([
            'date' => '2022-01-01',
        ]));

        $this->assertInstanceOf(DateString::class, $post->date);
        $this->assertEquals('Jan 1st, 2022', $post->date->short);
    }

    public function test_featured_image_can_be_constructed_returns_null_when_no_image_is_set_in_the_page_matter()
    {
        $page = new MarkdownPost();
        $this->assertNull($page->image);
    }

    public function test_featured_image_can_be_constructed_returns_image_object_with_local_path_when_matter_is_string()
    {
        $page = MarkdownPost::make(matter: ['image' => 'foo.png']);
        $image = $page->image;
        $this->assertInstanceOf(FeaturedImage::class, $image);
        $this->assertEquals('media/foo.png', $image->getSource());
    }

    public function test_featured_image_can_be_constructed_returns_image_object_with_remote_path_when_matter_is_string()
    {
        $page = MarkdownPost::make(matter: ['image' => 'https://example.com/foo.png']);
        $image = $page->image;
        $this->assertInstanceOf(FeaturedImage::class, $image);
        $this->assertEquals('https://example.com/foo.png', $image->getSource());
    }

    public function test_featured_image_can_be_constructed_returns_image_object_with_supplied_data_when_matter_is_array()
    {
        $page = MarkdownPost::make(matter: ['image' => ['source' => 'foo.png', 'titleText' => 'bar']]);
        $image = $page->image;
        $this->assertInstanceOf(FeaturedImage::class, $image);
        $this->assertEquals('media/foo.png', $image->getSource());
        $this->assertEquals('bar', $image->getTitleText());
    }
}
