<?php

namespace Tests\Unit;

use Hyde\Framework\Hyde;
use Tests\TestCase;

/**
 * Class FileHelperRelativeLinkTest.
 *
 * @covers \Hyde\Framework\Concerns\Internal\FileHelpers
 */
class FileHelperRelativeLinkTest extends TestCase
{
    // Test helper returns string as is when $current is not set
    public function test_helper_returns_string_as_is_if_current_is_not_set()
    {
        $this->assertEquals('foo/bar.html', Hyde::relativeLink('foo/bar.html'));
    }

    // Test helper injects the proper number of `../` 
    public function test_helper_injects_proper_number_of_doubles_slash()
    {
        $this->assertEquals('../index.html', Hyde::relativeLink('index.html', 'foo/bar.html'));
    }

    // Test helper injects the proper number of `../` for deeply nested $current paths
    public function test_helper_injects_proper_number_of_doubles_slash_for_deeply_nested_paths()
    {
        $this->assertEquals('../../../index.html', Hyde::relativeLink('index.html', 'foo/bar/baz/qux.html'));
    }

    // Test helper handles destination without file extension
    public function test_helper_handles_destination_without_file_extension()
    {
        $this->assertEquals('../index', Hyde::relativeLink('index', 'foo/bar.html'));
    }

    // Test helper handles $current without file extension
    public function test_helper_handles_current_without_file_extension()
    {
        $this->assertEquals('../index.html', Hyde::relativeLink('index.html', 'foo/bar'));
    }

    // Test helper handles case without any file extensions
    public function test_helper_handles_case_without_any_file_extensions()
    {
        $this->assertEquals('../index', Hyde::relativeLink('index', 'foo/bar'));
    }

    // Test helper handles case with mixed file extensions
    public function test_helper_handles_case_with_mixed_file_extensions()
    {
        $this->assertEquals('../index.md', Hyde::relativeLink('index.md', 'foo/bar.md'));
        $this->assertEquals('../index.txt', Hyde::relativeLink('index.txt', 'foo/bar.txt'));
    }

    // Test helper handles different file extensions
    public function test_helper_handles_different_file_extensions()
    {
        $this->assertEquals('../foo.png', Hyde::relativeLink('foo.png', 'foo/bar'));
        $this->assertEquals('../foo.css', Hyde::relativeLink('foo.css', 'foo/bar'));
        $this->assertEquals('../foo.js', Hyde::relativeLink('foo.js', 'foo/bar'));
    }
}
