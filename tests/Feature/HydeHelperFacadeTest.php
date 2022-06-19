<?php

namespace Hyde\Testing\Framework\Feature;

use Hyde\Framework\Helpers\Features;
use Hyde\Framework\Hyde;
use Hyde\Testing\TestCase;

/**
 * @covers \Hyde\Framework\Helpers\HydeHelperFacade
 */
class HydeHelperFacadeTest extends TestCase
{
    public function test_features_facade_returns_instance_of_features_class()
    {
        $this->assertInstanceOf(
            Features::class,
            Hyde::features()
        );
    }

    public function test_features_facade_can_be_used_to_call_static_methods_on_features_class()
    {
        $this->assertTrue(
            Hyde::features()->hasBlogPosts()
        );
    }

    public function test_hyde_has_feature_shorthand_calls_static_method_on_features_class()
    {
        $this->assertTrue(
            Hyde::hasFeature('blog-posts')
        );
    }
}
