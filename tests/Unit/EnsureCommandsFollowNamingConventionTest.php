<?php

namespace Hyde\Testing\Framework\Unit;

use Hyde\Testing\TestCase;

class EnsureCommandsFollowNamingConventionTest extends TestCase
{
    public function test_ensure_commands_follow_naming_convention()
    {
        $files = glob('vendor/hyde/framework/src/Commands/*.php');

        if (empty($files)) {
            $this->markTestSkipped('No commands found.');
        }

        foreach ($files as $filepath) {
            $filename = basename($filepath, '.php');
            $this->assertStringStartsWith('Hyde', $filename);
            $this->assertStringEndsWith('Command', $filename);
        }
    }
}
