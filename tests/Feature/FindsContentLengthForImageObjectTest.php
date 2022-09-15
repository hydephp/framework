<?php

namespace Hyde\Framework\Testing\Feature;

use Hyde\Framework\Models\Support\Image;
use Hyde\Testing\TestCase;
use Illuminate\Support\Facades\Http;

/**
 * @covers \Hyde\Framework\Actions\Constructors\FindsContentLengthForImageObject
 */
class FindsContentLengthForImageObjectTest extends TestCase
{
    /**
     * Unit test for the shorthand. Logic is tested in the rest of the case.
     *
     * @covers \Hyde\Framework\Models\Support\Image::getContentLength
     */
    public function test_image_helper_shorthand_returns_content_length()
    {
        $this->assertIsInt(
            (new Image(['path' => 'foo']))->getContentLength()
        );
    }

    public function test_it_can_find_the_content_length_for_a_local_image_stored_in_the_media_directory()
    {
        $image = new Image();
        $image->path = '_media/image.jpg';
        file_put_contents($image->path, '16bytelongstring');

        $this->assertEquals(
            16, $image->getContentLength()
        );

        unlink($image->path);
    }

    public function test_it_can_find_the_content_length_for_a_remote_image()
    {
        Http::fake(function () {
            return Http::response(null, 200, [
                'Content-Length' => 16,
            ]);
        });

        $image = new Image();
        $image->url = 'https://hyde.test/static/image.png';

        $this->assertEquals(
            16, $image->getContentLength()
        );
    }

    public function test_it_returns_0_if_local_image_is_missing()
    {
        $image = new Image();
        $image->path = '_media/image.jpg';

        $this->assertEquals(
            0, $image->getContentLength()
        );
    }

    public function test_it_returns_0_if_remote_image_is_missing()
    {
        Http::fake(function () {
            return Http::response(null, 404);
        });

        $image = new Image();
        $image->url = 'https://hyde.test/static/image.png';

        $this->assertEquals(
            0, $image->getContentLength()
        );
    }
}
