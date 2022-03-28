<?php

namespace Hyde\Framework\Commands;

use Hyde\Framework\Hyde;
use LaravelZero\Framework\Commands\Command;

/**
 * Hyde Command to print debug information.
 *
 * @deprecated 0.6.0 - Class will be renamed to DebugCommand
 */
class Debug extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'debug';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Print debug info';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $this->info('HydePHP Debug Screen');

        $this->newLine();
        $this->comment('Git Version: '.app('git.version'));
        $this->comment('Hyde Version: '.app('hyde.version'));
        $this->comment('Framework Version: '.app('framework.version'));

        $this->newLine();
        $this->comment('App Env: '.app('env'));

        $this->newLine();
        $this->line('Project directory:');
        $this->line(' > '.Hyde::path());

        $this->newLine();

        $this->line('Enabled features:');
        foreach (config('hyde.features') as $feature) {
            $this->line(" - $feature");
        }

        return 0;
    }
}
