<?php

namespace Tests\Feature\Services;

use Hyde\Framework\Services\BladeDownService;
use Tests\TestCase;

/**
 * Class BladeDownServiceTest.
 *
 * @covers \Hyde\Framework\Services\BladeDownService
 */
class BladeDownServiceTest extends TestCase
{
    // Test it renders Blade echo syntax
    public function test_it_renders_blade_echo_syntax()
    {
        $this->assertEquals('Hello World!', BladeDownService::render('[Blade]: {{ "Hello World!" }}'));
    }
    
    // Test it renders Blade within multiline Markdown
    public function test_it_renders_blade_within_multiline_markdown()
    {
        $this->assertEquals(
            "Foo\nHello World!\nBar",

            BladeDownService::render("Foo\n[Blade]: {{ 'Hello World!' }}\nBar")
        );
    }

    // Test it renders Blade views
    public function test_it_renders_blade_views()
    {
        file_put_contents(resource_path(
            'views/hello.blade.php'
        ), 'Hello World!');
     
        $this->assertEquals('Hello World!', BladeDownService::render('[Blade]: @include("hello")'));
     
        unlink(resource_path('views/hello.blade.php'));
    }

    // Test directive is case-insensitive
    public function test_directive_is_case_insensitive()
    {
        $this->assertEquals('Hello World!', BladeDownService::render('[blade]: {{ "Hello World!" }}'));
    }

    // Test space after directive is optional
    public function test_space_after_directive_is_optional()
    {
        $this->assertEquals('Hello World!', BladeDownService::render('[Blade]:{{ "Hello World!" }}'));
    }
}
