<?php

declare(strict_types=1);

namespace Hyde\Console\Commands;

use Hyde\Hyde;
use Hyde\Facades\Config;
use Composer\InstalledVersions;
use LaravelZero\Framework\Commands\Command;

use function str_replace;
use function realpath;
use function app;

/**
 * Hyde Command to print debug information.
 */
class DebugCommand extends Command
{
    /** @var string */
    protected $signature = 'debug';

    /** @var string */
    protected $description = 'Print debug info';

    public function __construct()
    {
        parent::__construct();

        if (Config::getString('app.env', 'production') !== 'development') {
            $this->setHidden();
        }
    }

    public function handle(): int
    {
        $this->info('HydePHP Debug Screen');

        $this->newLine();
        $this->comment('Git Version: '.app('git.version'));
        $this->comment('Hyde Version: '.(InstalledVersions::getPrettyVersion('hyde/hyde') ?: 'unreleased'));
        $this->comment('Framework Version: '.(InstalledVersions::getPrettyVersion('hyde/framework') ?: 'unreleased'));

        $this->newLine();
        $this->comment('App Env: '.app('env'));

        $this->newLine();
        if ($this->getOutput()->isVerbose()) {
            $this->line('Project directory:');
            $this->line(' > '.realpath(Hyde::path()));
            $this->line('Framework vendor path:');
            $this->line(' > '.(str_replace('/', DIRECTORY_SEPARATOR, Hyde::vendorPath()).' (vendor)'));
            $this->line(' > '.realpath(Hyde::vendorPath()).' (real)');
        } else {
            $this->comment('Project directory: '.Hyde::path());
        }

        $this->newLine();

        $this->line('Enabled features:');
        foreach ($this->enabledFeatures() as $feature) {
            $this->line(" - $feature");
        }

        return Command::SUCCESS;
    }

    /** @return array<string> */
    protected function enabledFeatures(): array
    {
        return Config::getArray('hyde.features');
    }
}
