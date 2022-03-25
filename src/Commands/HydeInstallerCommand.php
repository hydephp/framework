<?php

namespace Hyde\Framework\Commands;

use Hyde\Framework\Hyde;
use LaravelZero\Framework\Commands\Command;

use Hyde\Framework\Services\HydeInstaller;

class HydeInstallerCommand extends Command
{
    protected $signature = 'install';

    protected $description = 'Guides you through installing your new Hyde site';

    private HydeInstaller $installer;

    public function __construct()
    {
        parent::__construct();

        $this->installer = new HydeInstaller();
    }

    public function handle(): int
    {
        $this->title('🎩 Hyde Installer 🎩');
        $this->info('Welcome to HydePHP! Let\'s get you started! 🎉👌');

        $this->newLine();
        $this->line('The installer will guide you through the process of setting up your new Hyde site!');

        $this->installer->name = $this->ask('What is the name of your site?', $this->installer->name);

        $this->promptForUrl();

        $this->promptForHomepage();

        $this->newLine();
        $this->line('Almost done! Here are the settings, does it look right?');

        $this->printInstallerProperties();

        $prompt = $this->ask('Would you like to proceed?', 'Yes');
        if (str_contains(strtolower($prompt), 'y')) {
            $this->info('Okay, saving!');
            $this->installer->save();
        } else {
            $this->warn('Okay, aborting.');
            return 1;
        }
        
        if (sizeof($this->installer->warnings) > 0) {
            $this->printWarnings();
        }

        return 0;
    }

    private function promptForUrl()
    {
        if ($this->choice('Do you have a domain name you\'d like to set up?', [
            'no',
            'yes',
        ], 'no') === 'yes') {
            $this->installer->site_url = $this->installer->setSiteUrl(
                $this->ask('What is the url of your site?', $this->installer->site_url)
            );
            if (isset($this->installer->warnings['site_url_warning'])) {
                $this->warn($this->installer->warnings['site_url_warning']['message']);
                // Unset the warning so it is not shown again later
                unset($this->installer->warnings['site_url_warning']);
            }
        } else {
            $this->comment('Okay, skipping setting up URL.');
        }

    }

    private function promptForHomepage()
    {
        $this->newLine();
        $this->info('Hyde has a couple different homepages to choose from.');
        if (file_exists(Hyde::path('resources/views/pages/index.blade.php'))) {
            $this->warn('<error>Warning:</error> You already have a homepage file (resources/views/pages/index.blade.php). If you select \'yes\' it will be overwritten.');
        }

        if ($this->choice('Would you like to set the index.blade.php file?', [
            'no',
            'yes',
        ], 'no') === 'yes') {
            
            $options = [
                'welcome:   Default Welcome Page',
                'post-feed: Feed of Latest Posts',
                'blank:     A Blank Layout Page',
            ];

            $this->installer->homepage = strtok($this->choice(
                'Which homepage would you like to use?',
                $options,
                0
            ), ':');

        } else {
            $this->comment('Okay, skipping setting up a homepage.');
        }

    }

    private function printInstallerProperties()
    {
        $this->newLine();
        $this->info('Configured settings:');
        $this->newLine();

        $array = (array) $this->installer;
        foreach ($array as $key => $value) {
            if (is_bool($value)) {
                $value = $value ? 'true' : 'false';
            }
            if (empty($value) || is_array($value)) {
                continue;
            }
            $this->line("  <comment>$key</comment>: $value");
        }
        $this->newLine();
    }

    /**
     * @todo pretty print the values
     */
    private function printWarnings()
    {
        $this->newLine();
        $this->warn('There were some warnings during the install');

        foreach ($this->installer->warnings as $warning) {
            $this->line(print_r($warning));
        }

        $this->newLine();
    }
}
