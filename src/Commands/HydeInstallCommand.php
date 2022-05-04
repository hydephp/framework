<?php

namespace Hyde\Framework\Commands;

use Hyde\Framework\Concerns\Commands\AsksToRebuildSite;
use Hyde\Framework\Hyde;
use LaravelZero\Framework\Commands\Command;

/**
 * Initialize a new Hyde project.
 *
 * @see \Tests\Feature\Commands\HydeInstallCommandTest
 */
class HydeInstallCommand extends Command
{
    use AsksToRebuildSite;

    protected $signature = 'install';
    protected $description = 'Initialize a new Hyde project.';

    public ?string $siteName = null;
    public ?string $siteUrl = null;

    public function handle(): int
    {
        $this->title('Welcome to HydePHP!');

        $this->info('This guided installer is optional, but can help you to get set up quickly.');

        $this->warn('Please note that this installer should not be run in existing projects.');

        if (! $this->confirm('Do you want to continue?', true)) {
            $this->comment('Aborting installation.');

            return 130;
        }

        $this->info('Installing HydePHP...');
        $this->newLine();

        $this->call('update:configs');

        $this->promptForSiteName();

        $this->promptForSiteUrl();

        $this->promptForHomepage();

        $this->askToRebuildSite();

        return 0;
    }

    protected function promptForSiteName()
    {
        if ($this->siteName = $this->ask('What is the name of your site? (leave blank to skip)')) {
            $this->updateSiteName();
            $this->info('Site name set to: '.$this->siteName);

            return;
        }

        $this->line('Skipping site name.');
    }

    protected function promptForSiteUrl()
    {
        if ($this->siteUrl = $this->ask('What is the URL of your site? (leave blank to skip)')) {
            $this->line('The URL is used to create permalinks which can improve SEO.');
            $this->updateSiteUrl();
            $this->info('Site URL set to: '.$this->siteUrl);

            return;
        }

        $this->line('Skipping site URL.');
    }

    protected function promptForHomepage()
    {
        $this->info('Hyde has a few different homepage options.');
        if ($this->confirm('Would you like to select an index.blade.php file?')) {
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

    protected function updateSiteUrl(): void
    {
        $config = file_get_contents(Hyde::path('config/hyde.php'));
        $config = str_replace(
            "'site_url' => env('SITE_URL', null),",
            "'site_url' => env('SITE_URL', '".$this->siteUrl."'),",
            $config
        );
        file_put_contents(Hyde::path('config/hyde.php'), $config);
    }
}
