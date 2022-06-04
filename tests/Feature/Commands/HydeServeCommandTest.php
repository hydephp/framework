<?php

namespace Tests\Feature\Commands;

use Tests\TestCase;

/**
 * @covers \Hyde\Framework\Commands\HydeServeCommand
 */
class HydeServeCommandTest extends TestCase
{
    public function test_hyde_serve_command()
    {
        $this->artisan('serve')
            ->expectsOutput('Starting the server... Press Ctrl+C to stop')
            ->expectsOutput('This feature is experimental. Please report any issues on GitHub.')
            ->assertExitCode(0);
    }
}
