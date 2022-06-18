<?php

namespace Hyde\Testing\Framework\Feature\Commands;

use Hyde\Testing\TestCase;

/**
 * @covers \Hyde\Framework\Commands\HydeServeCommand
 */
class HydeServeCommandTest extends TestCase
{
    public function test_hyde_serve_command()
    {
        $this->artisan('serve')
            ->expectsOutput('Starting the server... Press Ctrl+C to stop')
            ->assertExitCode(0);
    }
}
