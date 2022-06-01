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

    public function test_process()
    {
        $this->markTestSkipped('TODO');
    }
}
