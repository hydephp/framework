<?php

declare(strict_types=1);

namespace Hyde\Framework\Testing\Feature;

use Hyde\Foundation\Services\LoadYamlConfiguration;
use Hyde\Testing\TestCase;
use Illuminate\Support\Facades\Config;
use function config;

/**
 * @covers \Hyde\Foundation\Services\LoadYamlConfiguration
 */
class LoadYamlConfigurationTest extends TestCase
{
    public function test_bootstrapper_applies_yaml_configuration_when_present()
    {
        $this->assertSame('HydePHP', config('hyde.name'));
        $this->file('hyde.yml', 'name: Foo');
        $this->app->bootstrapWith([LoadYamlConfiguration::class]);
        $this->assertSame('Foo', config('hyde.name'));
    }

    public function test_changes_in_yaml_file_override_changes_in_site_config()
    {
        $this->assertSame('HydePHP', Config::get('hyde.name'));
        $this->file('hyde.yml', 'name: Foo');
        $this->app->bootstrapWith([LoadYamlConfiguration::class]);
        $this->assertSame('Foo', Config::get('hyde.name'));
    }

    public function test_changes_in_yaml_file_override_changes_in_site_config_when_using_yaml_extension()
    {
        $this->assertSame('HydePHP', Config::get('hyde.name'));
        $this->file('hyde.yaml', 'name: Foo');
        $this->app->bootstrapWith([LoadYamlConfiguration::class]);
        $this->assertSame('Foo', Config::get('hyde.name'));
    }

    public function test_service_gracefully_handles_missing_file()
    {
        $this->app->bootstrapWith([LoadYamlConfiguration::class]);
        $this->assertSame('HydePHP', Config::get('hyde.name'));
    }

    public function test_service_gracefully_handles_empty_file()
    {
        $this->file('hyde.yml', '');
        $this->app->bootstrapWith([LoadYamlConfiguration::class]);
        $this->assertSame('HydePHP', Config::get('hyde.name'));
    }
}
