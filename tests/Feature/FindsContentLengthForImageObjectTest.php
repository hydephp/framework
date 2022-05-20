<?php

namespace Tests\Feature;

use Hyde\Framework\Actions\FindsContentLengthForImageObject;
use Hyde\Framework\Models\Image;
use Tests\TestCase;

/**
 * @covers \Hyde\Framework\Actions\FindsContentLengthForImageObject
 */
class FindsContentLengthForImageObjectTest extends TestCase
{
    /**
     * Unit test for the shorthand. Logic is tested in the rest of the case.
     * @covers \Hyde\Framework\Models\Image::getContentLength
     */
    public function test_image_helper_shorthand_returns_content_length()
    {
        $this->assertIsInt(
            (new Image())->getContentLength()
        );
    }

    // Test it can find the content length for a local image stored in the _media directory.
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
}
