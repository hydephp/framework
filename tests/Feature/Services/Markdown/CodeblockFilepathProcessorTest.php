<?php

namespace Tests\Feature\Services\Markdown;

use Hyde\Framework\Services\Markdown\CodeblockFilepathProcessor;
use Tests\TestCase;

/**
 * @covers \Hyde\Framework\Services\Markdown\CodeblockFilepathProcessor
 */
class CodeblockFilepathProcessorTest extends TestCase
{
    // Test preprocess method expands filepath
    public function test_preprocess_expands_filepath()
    {
        $markdown = "\n```php\n// filepath: foo.php\necho 'Hello World';\n```";
        $expected = "\n<!-- HYDE[Filepath]foo.php -->\n```php\necho 'Hello World';\n```";

        $this->assertEquals($expected, CodeblockFilepathProcessor::preprocess($markdown));
    }

    // Test preprocess method accepts multiple filepath formats
    public function test_preprocess_accepts_multiple_filepath_formats()
    {
        $patterns = [
            '// filepath: ',
            '// Filepath: ',
            '# filepath: ',
            '# Filepath: ',
            '// filepath ',
            '// Filepath ',
            '# filepath ',
            '# Filepath ',
        ];

        foreach ($patterns as $pattern) {
            $markdown = "\n```php\n{$pattern}foo.php\necho 'Hello World';\n```";
            $expected = "\n<!-- HYDE[Filepath]foo.php -->\n```php\necho 'Hello World';\n```";

            $this->assertEquals($expected, CodeblockFilepathProcessor::preprocess($markdown));
        }
    }

    // Test preprocess method accepts multiple languages
    public function test_preprocess_accepts_multiple_languages()
    {
        $languages = [
            'php',
            'js',
            'html',
            'made-up',
            'foo'
        ];

        foreach ($languages as $language) {

            $markdown = "\n```{$language}\n// filepath: foo.{$language}\nfoo\n```";
            $expected = "\n<!-- HYDE[Filepath]foo.{$language} -->\n```{$language}\nfoo\n```";

            $this->assertEquals($expected, CodeblockFilepathProcessor::preprocess($markdown));
        }

        $this->assertEquals($expected, CodeblockFilepathProcessor::preprocess($markdown));
    }

    // Test preprocess method accepts multiple input blocks
    public function test_preprocess_accepts_multiple_input_blocks()
    {
        $markdown = <<<MD
        
        ```php
        // filepath: foo.php
        echo 'Hello World';
        ```
        
        ```js
        // filepath: bar.js
        echo 'Hello World';
        ```
        MD;

        $expected = <<<MD
        
        <!-- HYDE[Filepath]foo.php -->
        ```php
        echo 'Hello World';
        ```
        
        <!-- HYDE[Filepath]bar.js -->
        ```js
        echo 'Hello World';
        ```
        MD;

        $this->assertEqualsIgnoringLineReturnType($expected, CodeblockFilepathProcessor::preprocess($markdown));
    }

    // Test preprocess method accepts multi-line codeblocks
    public function test_preprocess_accepts_multi_line_codeblocks()
    {
        $markdown = <<<MD
        
        ```php
        // filepath: foo.php
        echo 'Hello World';
        
        echo 'Hello World';
        ```
        MD;

        $expected = <<<MD
        
        <!-- HYDE[Filepath]foo.php -->
        ```php
        echo 'Hello World';
        
        echo 'Hello World';
        ```
        MD;

        $this->assertEqualsIgnoringLineReturnType($expected, CodeblockFilepathProcessor::preprocess($markdown));
    }

    public function test_process()
    {
        $this->markTestSkipped('TODO');
    }

    protected function assertEqualsIgnoringLineReturnType(string $expected, string $actual)
    {
        $this->assertEquals(str_replace("\r\n", "\n", $expected),
            str_replace("\r\n", "\n", $actual));
    }
}
