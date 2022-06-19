<?php

namespace Hyde\Testing\Framework\Feature\Services;

use Hyde\Framework\Services\Markdown\BladeDownProcessor;
use Hyde\Testing\TestCase;

/**
 * Class BladeDownProcessorTest.
 *
 * @covers \Hyde\Framework\Services\Markdown\BladeDownProcessor
 */
class BladeDownProcessorTest extends TestCase
{
    public function test_it_renders_blade_echo_syntax()
    {
        $this->assertEquals('Hello World!', BladeDownProcessor::render('[Blade]: {{ "Hello World!" }}'));
    }

    public function test_it_renders_blade_within_multiline_markdown()
    {
        $this->assertEquals(
            "Foo\nHello World!\nBar",

            BladeDownProcessor::render("Foo\n[Blade]: {{ 'Hello World!' }}\nBar")
        );
    }

    public function test_it_renders_blade_views()
    {
        file_put_contents(resource_path(
            'views/hello.blade.php'
        ), 'Hello World!');

        $this->assertEquals('Hello World!', BladeDownProcessor::render('[Blade]: @include("hello")'));

        unlink(resource_path('views/hello.blade.php'));
    }

    public function test_directive_is_case_insensitive()
    {
        $this->assertEquals('Hello World!', BladeDownProcessor::render('[blade]: {{ "Hello World!" }}'));
    }

    public function test_directive_is_ignored_if_it_is_not_at_the_start_of_a_line()
    {
        $this->assertEquals('Example: [Blade]: {{ "Hello World!" }}',
            BladeDownProcessor::render('Example: [Blade]: {{ "Hello World!" }}'));
    }

    public function test_it_renders_blade_echo_syntax_with_variables()
    {
        $this->assertEquals('Hello World!', BladeDownProcessor::render('[Blade]: {{ $foo }}', ['foo' => 'Hello World!']));
    }

    public function test_it_renders_blade_views_with_variables()
    {
        file_put_contents(resource_path(
            'views/hello.blade.php'
        ), 'Hello {{ $name }}!');

        $this->assertEquals('Hello John!', BladeDownProcessor::render('[Blade]: @include("hello", ["name" => "John"])'));

        unlink(resource_path('views/hello.blade.php'));
    }
}
