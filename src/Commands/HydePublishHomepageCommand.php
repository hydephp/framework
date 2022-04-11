<?php

namespace Hyde\Framework\Commands;

use Hyde\Framework\Actions\PublishesHomepageView;
use Hyde\Framework\Hyde;
use Hyde\Framework\Services\FileCacheService;
use Illuminate\Support\Facades\Artisan;
use LaravelZero\Framework\Commands\Command;

/**
 * Publish one of the default homepages.
 */
class HydePublishHomepageCommand extends Command
{
    protected $signature = 'publish:homepage {homepage? : The name of the page to publish}
                                {--force : Overwrite any existing files}';

    protected $description = 'Publish one of the default homepages to index.blade.php.';

    protected string $selected;

    public function handle(): int
    {
        $this->selected = $this->argument('homepage') ?? $this->promptForHomepage();

        $returnValue = (new PublishesHomepageView(
            $this->selected,
            $this->canExistingIndexFileBeOverwritten()
        ))->execute();

        if ($returnValue === true) {
            $this->info('Homepage published successfully!');
        } else {
            if (is_numeric($returnValue)) {
                if ($returnValue == 404) {
                    $this->error('Homepage '.$this->selected.' does not exist.');

                    return 404;
                }

                if ($returnValue == 409) {
                    $this->error('A modified index.blade.php file already exists. Use --force to overwrite.');

                    return 409;
                }
            }
        }

        $this->askToRebuildSite();

        return 0;
    }

    protected function promptForHomepage(): string
    {
        $choice = $this->choice(
            'Which homepage do you want to publish?',
            $this->formatPublishableChoices(),
            0
        );

        $choice = $this->parseChoiceIntoKey($choice);

        $this->line("<info>Selected page</info> [<comment>$choice</comment>]");
        $this->newLine();

        return $choice;
    }

    protected function formatPublishableChoices(): array
    {
        $keys = [];
        foreach (PublishesHomepageView::$homePages as $key => $value) {
            $keys[] = "<comment>$key</comment>: {$value['description']}";
        }

        return $keys;
    }

    protected function parseChoiceIntoKey(string $choice): string
    {
        return strstr(str_replace(['<comment>', '</comment>'], '', $choice), ':', true);
    }

    /** @deprecated v0.10.0 will be moved into shared trait.  */
    protected function askToRebuildSite()
    {
        if ($this->option('no-interaction')) {
            return;
        }

        if ($this->confirm('Would you like to rebuild the site?', 'Yes')) {
            $this->line('Okay, building site!');
            Artisan::call('build');
            $this->info('Site is built!');
        } else {
            $this->line('Okay, you can always run the build later!');
        }
    }

    protected function canExistingIndexFileBeOverwritten(): bool
    {
        if (! file_exists(Hyde::path('_pages/index.blade.php')) || $this->option('force')) {
            return true;
        }

        return FileCacheService::checksumMatchesAny(FileCacheService::unixsumFile(
            Hyde::path('_pages/index.blade.php')
        )) ?? $this->option('force');
    }
}
