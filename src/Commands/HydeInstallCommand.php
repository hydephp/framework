<?php

namespace Hyde\Framework\Commands;

use Hyde\Framework\Hyde;
use LaravelZero\Framework\Commands\Command;

/**
 * Initialize a new Hyde project.
 *
 * @see \Tests\Feature\Commands\HydeInstallCommandTest
 */
class HydeInstallCommand extends Command
{
    protected $signature = 'install';
    protected $description = 'Initialize a new Hyde project.';

    public ?string $siteName = null;

    public function handle(): int
    {
        $this->title('Welcome to HydePHP!');

        $this->info('This guided installer is optional, but can help you to get set up quickly.');

        $this->warn('Please note that this installer should not be run in existing projects.');

        if (!$this->confirm('Do you want to continue?', true)) {
            $this->comment('Aborting installation.');
            return 130;
        }

        $this->info('Installing HydePHP...');
        $this->newLine();

        $this->call('update:configs');

        $this->promptForSiteName();

        $this->promptForHomepage();

        return 0;
    }

    protected function promptForSiteName()
    {
        if ($this->siteName = $this->ask('What is the name of your site? (leave blank to skip)')) {
            $this->updateSiteName();
            $this->info('Site name set to: ' . $this->siteName);
            return;
        }

        $this->line('Skipping site name.');
    }

    protected function promptForHomepage()
    {
        $this->info('Hyde has a few different homepage options.');
        if ($this->confirm('Would you like to select one?')) {
            $this->call('publish:homepage');
        } else {
            $this->line('Okay, leaving the default homepage.');
        }
    }

    protected function updateSiteName(): void
    {
        $config = file_get_contents(Hyde::path('config/hyde.php'));
        $config = str_replace(
            "'name' => env('SITE_NAME', 'HydePHP'),",
            "'name' => env('SITE_NAME', '".$this->siteName."'),",
            $config
        );
        file_put_contents(Hyde::path('config/hyde.php'), $config);
    }
}
