<?php

declare(strict_types=1);

namespace Hyde\Framework\Testing\Feature;

use Hyde\Framework\Factories\FeaturedImageFactory;
use Hyde\Framework\Features\Blogging\Models\FeaturedImage;
use Hyde\Markdown\Models\FrontMatter;
use Hyde\Testing\TestCase;
use Illuminate\Support\Facades\Http;

/**
 * @covers \Hyde\Framework\Features\Blogging\Models\FeaturedImage
 *
 * @see \Hyde\Framework\Testing\Unit\FeaturedImageUnitTest
 */
class FeaturedImageTest extends TestCase
{
    public function testGetMetadataArray()
    {
        $this->assertSame([
            'url' => 'media/source',
            'contentUrl' => 'media/source',
        ], (new NullImage)->getMetadataArray());

        $this->assertSame([
            'text' => 'alt',
            'name' => 'title',
            'url' => 'media/source',
            'contentUrl' => 'media/source',
        ], (new FilledImage)->getMetadataArray());

        $this->assertSame([
            'url' => 'media/source',
            'contentUrl' => 'media/source',
        ], (new LocalImage)->getMetadataArray());

        $this->assertSame([
            'url' => 'https://example.com',
            'contentUrl' => 'https://example.com',
        ], (new RemoteImage)->getMetadataArray());
    }

    public function testCanConstructFeaturedImage()
    {
        $image = new FeaturedImage('_media/foo', ...$this->defaultArguments());
        $this->assertInstanceOf(FeaturedImage::class, $image);

        $this->assertEquals('media/foo', $image->getSource());
    }

    public function testFeaturedImageGetContentLength()
    {
        $this->file('_media/foo', 'image');

        $image = new FeaturedImage('_media/foo', ...$this->defaultArguments());
        $this->assertEquals(5, $image->getContentLength());
    }

    public function testFeaturedImageGetContentLengthWithRemoteSource()
    {
        Http::fake(function () {
            return Http::response(null, 200, [
                'Content-Length' => 16,
            ]);
        });

        $image = new FeaturedImage('https://hyde.test/static/image.png', ...$this->defaultArguments());
        $this->assertEquals(16, $image->getContentLength());
    }

    public function testFeaturedImageGetContentLengthWithRemoteSourceAndNotFoundResponse()
    {
        Http::fake(function () {
            return Http::response(null, 404);
        });

        $image = new FeaturedImage('https://hyde.test/static/image.png', ...$this->defaultArguments());
        $this->assertEquals(0, $image->getContentLength());
    }

    public function testFeaturedImageGetContentLengthWithRemoteSourceAndInvalidResponse()
    {
        Http::fake(function () {
            return Http::response(null, 200, [
                'Content-Length' => 'foo',
            ]);
        });

        $image = new FeaturedImage('https://hyde.test/static/image.png', ...$this->defaultArguments());
        $this->assertEquals(0, $image->getContentLength());
    }

    public function testGetSourceMethod()
    {
        $this->assertEquals('media/foo', (new FeaturedImage('_media/foo', ...$this->defaultArguments()))->getSource());

        $this->assertEquals('media/foo', FeaturedImageFactory::make(new FrontMatter(['image.source' => 'foo']))->getSource());
        $this->assertEquals('media/foo', FeaturedImageFactory::make(new FrontMatter(['image.source' => 'media/foo']))->getSource());
        $this->assertEquals('media/foo', FeaturedImageFactory::make(new FrontMatter(['image.source' => '_media/foo']))->getSource());

        $this->assertEquals('media/foo', FeaturedImageFactory::make(new FrontMatter(['image.source' => 'foo']))->getSource());
        $this->assertEquals('//foo', FeaturedImageFactory::make(new FrontMatter(['image.source' => '//foo']))->getSource());
        $this->assertEquals('http', FeaturedImageFactory::make(new FrontMatter(['image.source' => 'http']))->getSource());

        $this->assertEquals('media/foo', FeaturedImageFactory::make(new FrontMatter(['image' => 'foo']))->getSource());
        $this->assertEquals('http', FeaturedImageFactory::make(new FrontMatter(['image' => 'http']))->getSource());
    }

    protected function defaultArguments(): array
    {
        return ['alt', 'title', 'author', 'authorUrl', 'copyright', 'license', 'licenseUrl'];
    }
}

class LocalImage extends FeaturedImage
{
    public function __construct()
    {
        parent::__construct('source');
    }
}

class RemoteImage extends FeaturedImage
{
    public function __construct()
    {
        parent::__construct('https://example.com');
    }
}

class NullImage extends FeaturedImage
{
    public function __construct()
    {
        parent::__construct('source');
    }
}

class FilledImage extends FeaturedImage
{
    public function __construct()
    {
        parent::__construct('source', 'alt', 'title', 'author', 'authorUrl', 'license', 'licenseUrl', 'copyright');
    }
}
