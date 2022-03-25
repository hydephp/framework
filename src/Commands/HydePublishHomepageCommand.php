<?php

namespace Hyde\Framework\Commands;

use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;

/**
 * Publish one of the default homepages
 */
class HydePublishHomepageCommand extends BasePublishingCommand
{
    protected $signature = 'publish:homepage
                                {homepage? : The name of the page to publish}
                                {--force : Overwrite any existing files}';

    protected $description = 'Publish one of the default homepages';


    /**
     * Create a new command instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }
    
    /**
     * Execute the console command.
     *
     * @return int
     * @throws \League\Flysystem\FilesystemException
     */
    public function handle(): int
    {
        if ($this->argument('homepage') !== null) {
            $this->tags = [$this->argument('homepage')];
        } else {
            $this->determineWhatShouldBePublished();
        }

        foreach ($this->tags ?: [null] as $tag) {
            $this->publishTag($tag);
        }

        $this->info('Published selected homepage');

        if (!$this->option('no-interaction')) {
            $this->postHandleHook();
        }

        return 0;
    }


    /**
     * Prompt for which tag to publish.
     *
     * @return void
     */
    protected function promptForProviderOrTag()
    {
        $choice = $this->choice(
            "Which homepage do you want to publish?",
            choices: $this->publishableChoices(),
            default: 2
        );

        $this->line('<info>Selected page </info>[<comment>'. str_replace('homepage-', '', $choice).'</comment>]');
        $this->newLine();

        $this->parseChoice($choice);
    }

    /**
     * Parse the answer that was given via the prompt.
     *
     * @param string $choice
     * @return void
     */
    protected function parseChoice(string $choice)
    {
        $this->tags = [$choice];
    }

    /**
     * The choices available via the prompt.
     *
     * @return array
     */
    protected function publishableChoices(): array
    {
        return array_merge(
            [],
            Arr::sort(
                array_flip(array_filter(
                    array_flip(ServiceProvider::publishableGroups()),
                    fn($key) => str_starts_with($key, 'homepage-'),
                    ARRAY_FILTER_USE_KEY
                ))
            )
        );
    }

    protected function postHandleHook()
    {
        $prompt = $this->ask('Would you like to rebuild the site?', 'Yes');
        if (str_contains(strtolower($prompt), 'y')) {
            $this->line('Okay, building site!');
            Artisan::call('build');
            $this->info('Site is built!');
        } else {
            $this->line('Okay, you can always run the build later!');
        }
    }
}
