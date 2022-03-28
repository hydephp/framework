<?php

namespace Hyde\Framework\Commands;

use Hyde\Framework\Actions\CreatesDefaultDirectories;
use Hyde\Framework\Hyde;
use Illuminate\Support\Facades\File;
use LaravelZero\Framework\Commands\Command;

/**
 * Publish test file stubs.
 * @internal
 */
class PublishStubsCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'stubs:publish {--clean : Should all _directories be emptied first?} 
                {--force : Should the command be allowed to run in production?}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Publish the test stubs';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        if ($this->option('clean')) {
            if ((config('app.env', 'production') === 'development') || $this->option('force')) {
                $this->error('Purging files, I hope you know what you are doing.');
                $this->purge();
            } else {
                $this->error('This command cannot be run in production unless the force flag is set.');

                return 403;
            }
        }

        $this->info('Publishing test stubs');

        File::copyDirectory(Hyde::path('vendor/hyde/framework/tests/stubs/_posts'), Hyde::path('_posts'));
        File::copyDirectory(Hyde::path('vendor/hyde/framework/tests/stubs/_data'), Hyde::path('_data'));
        File::copyDirectory(Hyde::path('vendor/hyde/framework/tests/stubs/_media'), Hyde::path('_media'));
        File::copyDirectory(Hyde::path('vendor/hyde/framework/tests/stubs/_pages'), Hyde::path('_pages'));

        // Note that this overwrites existing files, though since this command should never be run
        // outside of testing I think it's okay.
        copy(
            Hyde::path('vendor/hyde/framework/resources/views/homepages/post-feed.blade.php'),
            Hyde::path('resources/views/pages/index.blade.php')
        );

        copy(
            Hyde::path('vendor/hyde/framework/resources/views/pages/404.blade.php'),
            Hyde::path('resources/views/pages/404.blade.php')
        );

        $this->info('Done!');

        return 0;
    }

    
    /**
     * Clear all the _content directories before publishing the stubs.
     * @return void
     */
    public function purge()
    {
        $this->warn('Removing all _content directories.');

        File::deleteDirectory(Hyde::path('_data'));
        File::deleteDirectory(Hyde::path('_docs'));
        File::deleteDirectory(Hyde::path('_drafts'));
        File::deleteDirectory(Hyde::path('_media'));
        File::deleteDirectory(Hyde::path('_pages'));
        File::deleteDirectory(Hyde::path('_posts'));
        File::deleteDirectory(Hyde::path('_site'));

        $this->line('<fg=gray> > Directories purged');

        $this->line(' > Recreating directories');
        (new CreatesDefaultDirectories)->__invoke();

        $this->line('</>');
    }
}
