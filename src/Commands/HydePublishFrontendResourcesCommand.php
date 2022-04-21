<?php

namespace Hyde\Framework\Commands;

use Hyde\Framework\Actions\PublishesDefaultFrontendResourceFiles;
use Hyde\Framework\Hyde;
use LaravelZero\Framework\Commands\Command;

/**
 * When updating Hyde, you may need to run this command to update the frontend resource files.
 */
class HydePublishFrontendResourcesCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'update:resources {--force : Overwrite existing files without asking.}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'When updating Hyde, you may need to run this command to update the frontend resource files.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $this->info('Publishing frontend resources!');
        if (! $this->option('force')) {
            $this->newLine();
            $this->warn('Please note that the following files will be overwritten:');
            foreach (PublishesDefaultFrontendResourceFiles::$files as $file) {
                $this->line('  - resources/assets/'.$file);
            }

            $this->warn('You should make sure you have a backup of these files before proceeding. Tip: Use Git!');
            $this->newLine();
            if (! $this->confirm('Would you like to continue?', true)) {
                $this->line('Okay. Aborting.');

                return 1;
            } else {
                $this->line('Okay. Proceeding.');
            }
        }

        (new PublishesDefaultFrontendResourceFiles(true))->__invoke();

        $this->info('Done!');

        return 0;
    }
}
