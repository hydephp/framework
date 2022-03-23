<?php

namespace Hyde\Framework\Commands;

use Hyde\Framework\Commands\BasePublishingCommand;

use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;

/**
 * Publish one of the default homepages
 */
class HydePublishHomepageCommand extends BasePublishingCommand
{
    protected $signature = 'publish:homepage {--force : Overwrite any existing files}';

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
     * Prompt for which tag to publish.
     *
     * @return void
     */
    protected function promptForProviderOrTag()
    {
        $choice = $this->choice(
            "Which homepage do you want to publish?",
            $choices = $this->publishableChoices()
        );

        $this->parseChoice($choice);
    }

    /**
     * The choices available via the prompt.
     *
     * @return array
     */
    protected function publishableChoices()
    {
        return array_merge(
            [],
            preg_filter('/^/', '<comment>Tag: </comment>', Arr::sort(
                array_flip(array_filter(
                    array_flip(ServiceProvider::publishableGroups()),
                    fn($key) => str_starts_with($key, 'homepage-'),
                    ARRAY_FILTER_USE_KEY
                ))
            ))
        );
    }

    protected function postHandleHook()
    {
        $prompt = $this->ask('Would you like to rebuild the site?', 'Yes');
        if (str_contains(strtolower($prompt), 'y')) {
            Artisan::call('build');
        };
    }

}
