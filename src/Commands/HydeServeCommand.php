<?php

namespace Hyde\Framework\Commands;

use Hyde\Framework\Hyde;
use LaravelZero\Framework\Commands\Command;

/**
 * Start the realtime compiler server.
 */
class HydeServeCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'serve {--port=8080} {--host=localhost}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Start the experimental realtime compiler.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        if (! $this->canRunServer()) {
            $this->error('Could not start the server.');

            return 1;
        }

        $this->line('<info>Starting the server...</info> Press Ctrl+C to stop');

        $this->warn('This feature is experimental. Please report any issues on GitHub.');

        $port = $this->option('port');
        $host = $this->option('host');
        $command = "php -S $host:$port ".Hyde::path('vendor/hyde/realtime-compiler/server.php');
        if (app()->environment('testing')) {
            $command = 'echo ' . $command;
        }
        passthru($command);

        return 0;
    }

    /**
     * Check if the server can be started.
     *
     * @return bool
     */
    protected function canRunServer(): bool
    {
        if (! file_exists(Hyde::path('vendor/hyde/realtime-compiler/server.php'))) {
            $this->warn('The realtime compiler extension is not installed.');

            return false;
        }

        return true;
    }
}
