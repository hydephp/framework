<?php

declare(strict_types=1);

namespace Hyde\Framework\Testing\Unit;

use PHPUnit\Framework\TestCase;

class EnsureCommandsFollowNamingConventionTest extends TestCase
{
    public function test_ensure_commands_follow_naming_convention()
    {
        $files = glob('vendor/hyde/framework/src/Console/Commands/*.php');

        $this->assertNotEmpty($files, 'No commands found.');

        foreach ($files as $filepath) {
            $filename = basename($filepath, '.php');
            $this->assertStringStartsNotWith('Hyde', $filename);
            $this->assertStringEndsWith('Command', $filename);
        }
    }
}
