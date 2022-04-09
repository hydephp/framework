<?php

namespace Hyde\Framework\Commands;

use Hyde\Framework\Services\FileCacheService;
use Hyde\Framework\Actions\PublishesHomepageView;
use Hyde\Framework\Hyde;
use LaravelZero\Framework\Commands\Command;
use Illuminate\Support\Facades\Artisan;

/**
 * Publish one of the default homepages.
 */
class HydePublishHomepageCommand extends Command
{
    protected $signature = 'publish:homepage
                                {homepage? : The name of the page to publish}
                                {--force : Overwrite any existing files}';

    protected $description = 'Publish one of the default homepages to index.blade.php.';

    protected string $selected;

    public function handle(): int
    {
        $this->selected = $this->argument('homepage') ?? $this->promptForFile();

        $status = (new PublishesHomepageView($this->selected, $this->canOverwriteFile()))->execute();

        if ($status === true) {
            $this->info("Homepage published successfully!");
        } else {
            if (is_numeric($status)) {
                if ($status == 404) {
                    $this->error('Homepage ' . $this->selected . ' does not exist.');
                    return 404;
                }

                // Since only two possible errors are possible, we know that the error is a 409 conflict.
                if ($status == 409) {
                    $this->error('A homepage file already exists. Use --force to overwrite.');
                    return 409;
                }
            }
        }

        $this->postHandleHook();

        return 0;
    }

    /**
     * Prompt for which file to publish.
     *
     * @return string selected choice
     */
    protected function promptForFile(): string
    {
        $choice = $this->choice(
            'Which homepage do you want to publish?',
            choices: $this->publishableChoices(),
            default: 0
        );

        $choice = $this->parseChoice($choice);

        $this->line('<info>Selected page </info>[<comment>' . str_replace('homepage-', '', $choice) . '</comment>]');
        $this->newLine();

        return $choice;
    }

    /**
     * The choices available via the prompt.
     *
     * @return array
     */
    protected function publishableChoices(): array
    {
        $keys = [];
        foreach (PublishesHomepageView::$homePages as $key => $value) {
            $keys[] = '<comment>' . $key . '</comment>: ' . $value['description'];
        }
        return $keys;
    }

    /**
     * Parse the choice into a valid key.
     *
     * @param string $choice
     * @return string
     */
    protected function parseChoice(string $choice): string
    {
        // Strip the <comment> and </comment> tags
        $choice = str_replace('<comment>', '', $choice);
        $choice = str_replace('</comment>', '', $choice);
        // return everything before the colon
        return strstr($choice, ':', true);
    }

    /**
     * @deprecated v0.10.0 will be moved into shared trait.
     */
    protected function postHandleHook()
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

    /**
     * Allow default files to be overwritten.
     * @see https://github.com/hydephp/framework/issues/67
     */
    protected function canOverwriteFile(): bool
    {
        if (!file_exists(Hyde::path('resources/views/pages/index.blade.php')) || $this->option('force')) {
            return true;
        }

        return FileCacheService::checksumMatchesAny(md5_file(
            Hyde::path('resources/views/pages/index.blade.php')
        )) ?? $this->option('force');
    }
}
