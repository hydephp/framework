<?php

namespace Hyde\Framework\Testing\Unit;

use Hyde\Framework\Contracts\AssetServiceContract;
use Hyde\Framework\Facades\Asset;
use Hyde\Framework\Services\AssetService;
use Hyde\Testing\TestCase;

/**
 * @covers \Hyde\Framework\Facades\Asset
 */
class AssetFacadeTest extends TestCase
{
    public function test_asset_facade_returns_the_asset_service()
    {
        $this->assertInstanceOf(AssetServiceContract::class, Asset::getFacadeRoot());
    }

    public function test_facade_returns_same_instance_as_bound_by_the_container()
    {
        $this->assertSame(Asset::getFacadeRoot(), app(AssetServiceContract::class));
    }

    public function test_asset_facade_can_call_methods_on_the_asset_service()
    {
        $service = new AssetService();
        $this->assertEquals($service->version(), Asset::version());
    }
}
