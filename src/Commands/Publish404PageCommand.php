<?php

namespace Hyde\Framework\Commands;

use Hyde\Framework\Hyde;
use LaravelZero\Framework\Commands\Command;

class Publish404PageCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'publish:404 {--force : Overwrite any existing files}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Publish the 404 Blade page';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $source = Hyde::path('vendor/hyde/framework/resources/src/404.blade.php');
        $path = Hyde::path('resources/views/pages/404.blade.php');

        if (file_exists($path) && !$this->option('force')) {
            $this->error("File $path already exists!");
            return 409;
        }

        copy($source, $path);

        $this->info("Created file $path!");
        return 0;
    }
}
