<?php

namespace Tests\Feature\Commands;

use Hyde\Framework\Hyde;
use Tests\TestCase;

/**
 * @covers \Hyde\Framework\Commands\HydeInstallCommand
 */
class HydeInstallCommandTest extends TestCase
{
    public function test_command_output()
    {
        $this->artisan('install')
            ->expectsOutputToContain('Welcome to HydePHP!')
            ->expectsQuestion('Do you want to continue?', true)
            ->expectsOutput('Installing HydePHP...')
            ->doesntExpectOutput('Aborting installation.')
            ->expectsOutput('Hyde has a few different homepage options.')
            ->expectsQuestion('Would you like to select one?', false)
            ->assertExitCode(0);
    }

    public function test_command_exits_with_sigint_130_if_user_declines_confirmation()
    {
        $this->artisan('install')
            ->expectsOutputToContain('Welcome to HydePHP!')
            ->expectsQuestion('Do you want to continue?', false)
            ->expectsOutput('Aborting installation.')
            ->doesntExpectOutput('Installing HydePHP...')
            ->assertExitCode(130);
    }

    public function test_command_calls_publish_homepage_command()
    {
        $this->artisan('install')
            ->expectsOutputToContain('Welcome to HydePHP!')
            ->expectsQuestion('Do you want to continue?', true)
            ->expectsOutput('Installing HydePHP...')
            ->doesntExpectOutput('Aborting installation.')
            ->expectsOutput('Hyde has a few different homepage options.')
            ->expectsQuestion('Would you like to select one?', true)
            ->expectsQuestion('Which homepage do you want to publish?', 'default')
            ->assertExitCode(0);
    }

}
