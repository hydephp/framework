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
    public function testHasVersionString()
    {
        $service = new AssetService();
        $this->assertIsString($service->version);
    }

    public function testCanChangeVersion()
    {
        $service = new AssetService();
        $service->version = '1.0.0';
        $this->assertEquals('1.0.0', $service->version);
    }

    public function testVersionMethodReturnsVersionPropertyWhenConfigOverrideIsNotSet()
    {
        $service = new AssetService();
        $this->assertEquals($service->version, $service->version());
    }

    public function testCanChangeVersionInConfig()
    {
        $service = new AssetService();
        Config::set('hyde.cdnHydeFrontVersionOverride', '2.0.0');
        $this->assertEquals('2.0.0', $service->version());
    }

    public function testTailwindPathMethodReturnsFalseIfNullInConfig()
    {
        $service = new AssetService();
        Config::set('hyde.loadTailwindFromCDN');
        $this->assertFalse($service->tailwindPath());
    }


    public function testTailwindPathMethodReturnsFalseIfDisabledInConfig()
    {
        $service = new AssetService();
        Config::set('hyde.loadTailwindFromCDN', false);
        $this->assertFalse($service->tailwindPath());
    }

    public function testTailwindPathMethodReturnsCDNPathIfEnabledInConfig()
    {
        $service = new AssetService();
        Config::set('hyde.loadTailwindFromCDN', true);
        $this->assertIsString($service->tailwindPath());
        $this->assertStringContainsString('app.css', $service->tailwindPath());
    }

    public function testStylePathMethodReturnsCDNPath()
    {
        $service = new AssetService();
        $this->assertIsString($service->stylePath());
        $this->assertStringContainsString('hyde.css', $service->stylePath());
    }

    public function testScriptPathMethodReturnsCDNPath()
    {
        $service = new AssetService();
        $this->assertIsString($service->scriptPath());
        $this->assertStringContainsString('hyde.js', $service->scriptPath());
    }

    public function testCDNPathConstructorReturnsCDNURI()
    {
        $service = new AssetService();
        $this->assertIsString($path = $service->cdnPathConstructor('styles.css'));
        $this->assertStringContainsString('styles.css', $path);
    }

    public function testCDNPathConstructorUsesSelectedVersion()
    {
        $service = new AssetService();
        Config::set('hyde.cdnHydeFrontVersionOverride', '1.2.3');
        $this->assertStringContainsString('@1.2.3', $service->cdnPathConstructor('styles.css'));
    }

}
