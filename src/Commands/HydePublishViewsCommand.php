<?php

namespace Hyde\Framework\Commands;

use LaravelZero\Framework\Commands\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Events\VendorTagPublished;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use League\Flysystem\Filesystem as Flysystem;
use League\Flysystem\Local\LocalFilesystemAdapter as LocalAdapter;
use League\Flysystem\MountManager;
use League\Flysystem\UnixVisibility\PortableVisibilityConverter;
use League\Flysystem\Visibility;

/**
 * Publish the Hyde assets
 *
 * Based on Illuminate\Foundation\Console\VendorPublishCommand
 * @see https://github.com/laravel/framework/blob/9.x/src/Illuminate/Foundation/Console/VendorPublishCommand.php
 * @license MIT
 */
class HydePublishViewsCommand extends BasePublishingCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'publish:views {--force : Overwrite any existing files}
                    {--all : Publish all views without prompt}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Publish the Hyde resource view files for customization';
    


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
     * Determine the provider or tag(s) to publish.
     *
     * @return void
     */
    protected function determineWhatShouldBePublished()
    {
        if ($this->option('all')) {
            $this->tags = array_flip(array_filter(
                array_flip(ServiceProvider::publishableGroups()),
                fn($key) => str_starts_with($key, 'hyde-'),
                ARRAY_FILTER_USE_KEY
            ));
            return;
        }

        if (!$this->tags) {
            $this->promptForProviderOrTag();
        }
    }

    /**
     * Prompt for which tag to publish.
     *
     * @return void
     */
    protected function promptForProviderOrTag()
    {
        $choice = $this->choice(
            "Which view categories (tags) would you like to publish?",
            $choices = $this->publishableChoices()
        );

        if ($choice == $choices[0] || is_null($choice)) {
            $this->tags = array_flip(array_filter(
                array_flip(ServiceProvider::publishableGroups()),
                fn($key) => str_starts_with($key, 'hyde-'),
                ARRAY_FILTER_USE_KEY
            ));
            return;
        }

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
            ['<comment>Publish files from all tags listed below</comment>'],
            preg_filter('/^/', '<comment>Tag: </comment>', Arr::sort(
                array_flip(array_filter(
                    array_flip(ServiceProvider::publishableGroups()),
                    fn($key) => str_starts_with($key, 'hyde-'),
                    ARRAY_FILTER_USE_KEY
                ))
            ))
        );
    }
}
