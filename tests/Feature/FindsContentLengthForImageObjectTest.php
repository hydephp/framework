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
    // Test Image helper shorthand returns content length
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
}
