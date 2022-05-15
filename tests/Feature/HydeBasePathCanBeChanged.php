<?php

namespace Tests\Feature;

use Hyde\Framework\Hyde;
use Tests\TestCase;

/**
 * Class HydeBasePathCanBeChanged.
 *
 * @covers \Hyde\Framework\Hyde::getBasePath()
 * @covers \Hyde\Framework\Hyde::setBasePath()
 * @covers \Hyde\Framework\Hyde::path()
 */
class HydeBasePathCanBeChanged extends TestCase
{
    public function test_hyde_base_path_can_be_changed()
    {
        Hyde::setBasePath('/foo/bar');
        $this->assertEquals('/foo/bar', Hyde::getBasePath());
        $this->assertEquals('/foo/bar', Hyde::path());
    }
}
