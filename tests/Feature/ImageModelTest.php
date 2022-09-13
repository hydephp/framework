<?php

namespace Hyde\Framework\Testing\Feature;

use Hyde\Framework\Models\Image;
use Hyde\Testing\TestCase;

/**
 * @covers \Hyde\Framework\Models\Image
 */
class ImageModelTest extends TestCase
{
    public function test_can_construct_new_image()
    {
        $image = new Image();
        $this->assertInstanceOf(Image::class, $image);
    }

    public function test_make_can_create_an_image_based_on_string()
    {
        $image = Image::make('foo');
        $this->assertInstanceOf(Image::class, $image);
        $this->assertEquals('foo', $image->path);
    }

    public function test_make_can_create_an_image_based_on_array()
    {
        $image = Image::make([
            'path' => 'foo',
            'title' => 'bar',
        ]);
        $this->assertInstanceOf(Image::class, $image);
        $this->assertEquals('foo', $image->path);
        $this->assertEquals('bar', $image->title);
    }

    public function test_from_source_automatically_assigns_proper_property_depending_on_if_the_string_is_remote()
    {
        $image = Image::fromSource('https://example.com/image.jpg');
        $this->assertInstanceOf(Image::class, $image);
        $this->assertEquals('https://example.com/image.jpg', $image->url);

        $image = Image::fromSource('image.jpg');
        $this->assertInstanceOf(Image::class, $image);
        $this->assertEquals('image.jpg', $image->path);
    }

    public function test_array_data_can_be_used_to_initialize_properties_in_constructor()
    {
        $data = [
            'path' => 'image.jpg',
            'url' => 'https://example.com/image.jpg',
            'description' => 'This is an image',
            'title' => 'Image Title',
        ];

        $image = new Image($data);

        $this->assertEquals($data['path'], $image->path);
        $this->assertEquals($data['url'], $image->url);
        $this->assertEquals($data['description'], $image->description);
        $this->assertEquals($data['title'], $image->title);
    }

    public function test_get_source_method_returns_url_when_both_url_and_path_is_set()
    {
        $image = new Image();
        $image->url = 'https://example.com/image.jpg';
        $image->path = 'image.jpg';

        $this->assertEquals('https://example.com/image.jpg', $image->getSource());
    }

    public function test_get_source_method_returns_path_when_only_path_is_set()
    {
        $image = new Image();
        $image->path = 'image.jpg';

        $this->assertEquals('image.jpg', $image->getSource());
    }

    public function test_get_source_method_throws_exception_when_no_source_is_set()
    {
        $image = new Image();

        $this->expectExceptionMessage('Attempting to get source from Image that has no source.');
        $image->getSource();
    }

    public function test_get_source_method_does_not_throw_exception_when_path_is_set()
    {
        $image = new Image();
        $image->path = 'image.jpg';
        $this->assertEquals('image.jpg', $image->getSource());
    }

    public function test_get_source_method_does_not_throw_exception_when_url_is_set()
    {
        $image = new Image();
        $image->url = 'https://example.com/image.jpg';
        $this->assertEquals('https://example.com/image.jpg', $image->getSource());
    }

    public function test_get_image_author_attribution_string_method()
    {
        $image = new Image([
            'author' => 'John Doe',
            'credit' => 'https://example.com/',
        ]);
        $string = $image->getImageAuthorAttributionString();
        $this->assertStringContainsString('itemprop="creator"', $string);
        $this->assertStringContainsString('itemprop="url"', $string);
        $this->assertStringContainsString('itemtype="http://schema.org/Person"', $string);
        $this->assertStringContainsString('<span itemprop="name">John Doe</span>', $string);
        $this->assertStringContainsString('<a href="https://example.com/"', $string);

        $image = new Image(['author' => 'John Doe']);
        $string = $image->getImageAuthorAttributionString();
        $this->assertStringContainsString('itemprop="creator"', $string);
        $this->assertStringContainsString('itemtype="http://schema.org/Person"', $string);
        $this->assertStringContainsString('<span itemprop="name">John Doe</span>', $string);

        $image = new Image();
        $this->assertNull($image->getImageAuthorAttributionString());
    }

    public function test_get_copyright_string()
    {
        $image = new Image(['copyright' => 'foo']);
        $this->assertEquals('<span itemprop="copyrightNotice">foo</span>', $image->getCopyrightString());

        $image = new Image();
        $this->assertNull($image->getCopyrightString());
    }

    public function test_get_license_string()
    {
        $image = new Image([
            'license' => 'foo',
            'licenseUrl' => 'https://example.com/bar.html',
        ]);
        $this->assertEquals('<a href="https://example.com/bar.html" rel="license nofollow noopener" '.
                'itemprop="license">foo</a>', $image->getLicenseString());

        $image = new Image(['license' => 'foo']);
        $this->assertEquals('<span itemprop="license">foo</span>', $image->getLicenseString());

        $image = new Image(['licenseUrl' => 'https://example.com/bar.html']);
        $this->assertNull($image->getLicenseString());

        $image = new Image();
        $this->assertNull($image->getLicenseString());
    }

    public function test_get_fluent_attribution_method()
    {
        $image = new Image(['author' => 'John Doe']);
        $string = $image->getFluentAttribution();

        $this->assertStringContainsString('Image by ', $string);

        $image = new Image(['copyright' => 'foo']);
        $string = $image->getFluentAttribution();

        $this->assertStringContainsString('<span itemprop="copyrightNotice">foo</span>', $string);

        $image = new Image(['license' => 'foo']);

        $string = $image->getFluentAttribution();
        $this->assertStringContainsString('License <span itemprop="license">foo</span>', $string);

        $image = new Image();
        $this->assertEquals('', $image->getFluentAttribution());
    }

    public function test_get_metadata_array()
    {
        $image = new Image([
            'description' => 'foo',
            'title' => 'bar',
            'path' => 'image.jpg',
        ]);

        $this->assertEquals([
            'text' => 'foo',
            'name' => 'bar',
            'url' => 'media/image.jpg',
            'contentUrl' => 'media/image.jpg',
        ], $image->getMetadataArray());
    }

    public function test_get_metadata_array_with_remote_url()
    {
        $image = new Image([
            'url' => 'https://foo/bar',
        ]);

        $this->assertEquals([
            'url' => 'https://foo/bar',
            'contentUrl' => 'https://foo/bar',
        ], $image->getMetadataArray());
    }

    public function test_get_metadata_array_with_local_path()
    {
        $image = new Image([
            'path' => 'foo.png',
        ]);

        $this->assertEquals([
            'url' => 'media/foo.png',
            'contentUrl' => 'media/foo.png',
        ], $image->getMetadataArray());
    }

    public function test_get_metadata_array_with_local_path_when_on_nested_page()
    {
        $this->mockCurrentPage('foo/bar');
        $image = new Image([
            'path' => 'foo.png',
        ]);

        $this->assertEquals([
            'url' => '../media/foo.png',
            'contentUrl' => '../media/foo.png',
        ], $image->getMetadataArray());
    }

    public function test_get_link_resolves_remote_paths()
    {
        $image = new Image([
            'url' => 'https://example.com/image.jpg',
        ]);

        $this->assertEquals('https://example.com/image.jpg', $image->getLink());
    }

    public function test_get_link_resolves_local_paths()
    {
        $image = new Image([
            'path' => 'image.jpg',
        ]);

        $this->assertEquals('media/image.jpg', $image->getLink());
    }

    public function test_get_link_resolves_local_paths_when_on_nested_page()
    {
        $image = new Image([
            'path' => 'image.jpg',
        ]);

        $this->mockCurrentPage('foo/bar');
        $this->assertEquals('../media/image.jpg', $image->getLink());
    }

    public function test_local_path_is_normalized_to_the_media_directory()
    {
        $this->assertEquals('image.jpg', (new Image([
            'path' => 'image.jpg',
        ]))->path);

        $this->assertEquals('image.jpg', (new Image([
            'path' => '_media/image.jpg',
        ]))->path);

        $this->assertEquals('image.jpg', (new Image([
            'path' => 'media/image.jpg',
        ]))->path);
    }

    public function test_to_string_returns_the_image_source()
    {
        $this->assertEquals('https://example.com/image.jpg', (string) (new Image([
            'url' => 'https://example.com/image.jpg',
        ])));

        $this->assertEquals('media/image.jpg', (string) (new Image([
            'path' => 'image.jpg',
        ])));
    }

    public function test_to_string_returns_the_image_source_for_nested_pages()
    {
        $this->mockCurrentPage('foo/bar');
        $this->assertEquals('../media/image.jpg', (string) (new Image([
            'path' => 'image.jpg',
        ])));
    }
}
