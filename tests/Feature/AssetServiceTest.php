<?php

namespace Feature;

use Hyde\Framework\Services\AssetService;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

/**
 * @covers \Hyde\Framework\Services\AssetService
 */
class AssetServiceTest extends TestCase
{
    public function test_has_version_string()
    {
        $service = new AssetService();
        $this->assertIsString($service->version);
    }

    public function test_can_change_version()
    {
        $service = new AssetService();
        $service->version = '1.0.0';
        $this->assertEquals('1.0.0', $service->version);
    }

    public function test_version_method_returns_version_property_when_config_override_is_not_set()
    {
        $service = new AssetService();
        $this->assertEquals($service->version, $service->version());
    }

    public function test_can_change_version_in_config()
    {
        $service = new AssetService();
        Config::set('hyde.cdnHydeFrontVersionOverride', '2.0.0');
        $this->assertEquals('2.0.0', $service->version());
    }

    public function test_tailwind_path_method_returns_false_if_null_in_config()
    {
        $service = new AssetService();
        Config::set('hyde.loadTailwindFromCDN');
        $this->assertFalse($service->tailwindPath());
    }


    public function test_tailwind_path_method_returns_false_if_disabled_in_config()
    {
        $service = new AssetService();
        Config::set('hyde.loadTailwindFromCDN', false);
        $this->assertFalse($service->tailwindPath());
    }

    public function test_tailwind_path_method_returns_cdn_path_if_enabled_in_config()
    {
        $service = new AssetService();
        Config::set('hyde.loadTailwindFromCDN', true);
        $this->assertIsString($service->tailwindPath());
        $this->assertStringContainsString('app.css', $service->tailwindPath());
    }

    public function test_style_path_method_returns_cdn_path()
    {
        $service = new AssetService();
        $this->assertIsString($service->stylePath());
        $this->assertStringContainsString('hyde.css', $service->stylePath());
    }

    public function test_script_path_method_returns_cdn_path()
    {
        $service = new AssetService();
        $this->assertIsString($service->scriptPath());
        $this->assertStringContainsString('hyde.js', $service->scriptPath());
    }

    public function test_cdn_path_constructor_returns_cdn_uri()
    {
        $service = new AssetService();
        $this->assertIsString($path = $service->cdnPathConstructor('styles.css'));
        $this->assertStringContainsString('styles.css', $path);
    }

    public function test_cdn_path_constructor_uses_selected_version()
    {
        $service = new AssetService();
        Config::set('hyde.cdnHydeFrontVersionOverride', '1.2.3');
        $this->assertStringContainsString('@1.2.3', $service->cdnPathConstructor('styles.css'));
    }

}
