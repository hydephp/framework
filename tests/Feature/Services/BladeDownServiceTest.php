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

    // Test directive is ignored if it's not at the start of a line
    public function test_directive_is_ignored_if_it_is_not_at_the_start_of_a_line()
    {
        $this->assertEquals('Example: [Blade]: {{ "Hello World!" }}',
            BladeDownService::render('Example: [Blade]: {{ "Hello World!" }}'));
    }

    // Test it renders Blade views with variables
    public function test_it_renders_blade_views_with_variables()
    {
        file_put_contents(resource_path(
            'views/hello.blade.php'
        ), 'Hello {{ $name }}!');
     
        $this->assertEquals('Hello John!', BladeDownService::render('[Blade]: @include("hello", ["name" => "John"])'));
     
        unlink(resource_path('views/hello.blade.php'));
    }
}
