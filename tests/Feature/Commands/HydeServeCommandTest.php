<?php

namespace Hyde\Framework\Testing\Feature\Commands;

use Hyde\Testing\TestCase;

/**
 * @covers \Hyde\Framework\Commands\HydeServeCommand
 */
class HydeServeCommandTest extends TestCase
{
    public function test_hyde_serve_command()
    {
        $this->artisan('serve')
            ->expectsOutput('Starting the HydeRC server... Press Ctrl+C to stop')
            ->assertExitCode(0);
    }
}
