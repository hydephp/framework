<?php

namespace Hyde\Framework\Testing\Feature\Commands;

use Hyde\Framework\Hyde;
use Hyde\Testing\TestCase;

class MakePostCommandTest extends TestCase
{
    /**
     * Get the path of the test Markdown file.
     *
     * @return string
     */
    public function getPath(): string
    {
        return Hyde::path('_posts/test-post.md');
    }

    /**
     * Clean up after tests by removing the created file.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unlinkIfExists($this->getPath());

        parent::tearDown();
    }

    public function test_command_has_expected_output_and_creates_valid_file()
    {
        // Assert that no old file exists which would cause issues
        $this->assertFileDoesNotExist($this->getPath());

        $this->artisan('make:post')
            ->expectsQuestion('What is the title of the post?', 'Test Post')
            ->expectsQuestion('Write a short post excerpt/description', 'A short description')
            ->expectsQuestion('What is your (the author\'s) name?', 'PHPUnit')
            ->expectsQuestion('What is the primary category of the post?', 'general')
            ->expectsOutput('Creating a post with the following details:')
            ->expectsConfirmation('Do you wish to continue?', 'yes')

            ->assertExitCode(0);

        $this->assertFileExists($this->getPath());
        $this->assertStringContainsString(
            'title: Test Post',
            file_get_contents($this->getPath())
        );
    }

    public function test_that_files_are_not_overwritten_when_force_flag_is_not_set()
    {
        file_put_contents($this->getPath(), 'This should not be overwritten');
        $this->artisan('make:post')
            ->expectsQuestion('What is the title of the post?', 'Test Post')
            ->expectsQuestion('Write a short post excerpt/description', 'A short description')
            ->expectsQuestion('What is your (the author\'s) name?', 'PHPUnit')
            ->expectsQuestion('What is the primary category of the post?', 'general')
            ->expectsOutput('Creating a post with the following details:')

            ->expectsConfirmation('Do you wish to continue?', 'yes')
            ->expectsOutput('If you want to overwrite the file supply the --force flag.')

            ->assertExitCode(409);

        $this->assertStringContainsString(
            'This should not be overwritten',
            file_get_contents($this->getPath())
        );
    }

    public function test_that_files_are_overwritten_when_force_flag_is_set()
    {
        file_put_contents($this->getPath(), 'This should be overwritten');
        $this->artisan('make:post --force')
            ->expectsQuestion('What is the title of the post?', 'Test Post')
            ->expectsQuestion('Write a short post excerpt/description', 'A short description')
            ->expectsQuestion('What is your (the author\'s) name?', 'PHPUnit')
            ->expectsQuestion('What is the primary category of the post?', 'general')
            ->expectsOutput('Creating a post with the following details:')
            ->expectsConfirmation('Do you wish to continue?', 'yes')

            ->assertExitCode(0);

        $this->assertStringNotContainsString(
            'This should be overwritten',
            file_get_contents($this->getPath())
        );
        $this->assertStringContainsString(
            'title: Test Post',
            file_get_contents($this->getPath())
        );
    }

    public function test_that_title_can_be_specified_in_command_signature()
    {
        $this->artisan('make:post "Test Post"')
            ->expectsOutputToContain('Selected title: Test Post')
            ->expectsQuestion('Write a short post excerpt/description', 'A short description')
            ->expectsQuestion('What is your (the author\'s) name?', 'PHPUnit')
            ->expectsQuestion('What is the primary category of the post?', 'general')
            ->expectsConfirmation('Do you wish to continue?', 'yes')

            ->assertExitCode(0);
    }

    public function test_that_command_can_be_canceled()
    {
        $this->artisan('make:post "Test Post"')
        ->expectsOutputToContain('Selected title: Test Post')
        ->expectsQuestion('Write a short post excerpt/description', 'A short description')
        ->expectsQuestion('What is your (the author\'s) name?', 'PHPUnit')
        ->expectsQuestion('What is the primary category of the post?', 'general')
        ->expectsConfirmation('Do you wish to continue?')
        ->expectsOutput('Aborting.')
        ->assertExitCode(130);

        $this->assertFileDoesNotExist($this->getPath());
    }
}
