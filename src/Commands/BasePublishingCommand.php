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
 * Base command to publish the Hyde assets
 *
 * @internal
 *
 * Based on Illuminate\Foundation\Console\VendorPublishCommand
 * @see https://github.com/laravel/framework/blob/9.x/src/Illuminate/Foundation/Console/VendorPublishCommand.php
 * @license MIT
 */
abstract class BasePublishingCommand extends Command
{
    protected $signature;
    protected $description;

    abstract protected function publishableChoices();

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;
    
    /**
     * The provider to publish.
     *
     * @var string
     */
    protected $provider = null;


    /**
     * The tags to publish.
     *
     * @var array
     */
    protected $tags = [];

    
    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $this->determineWhatShouldBePublished();

        foreach ($this->tags ?: [null] as $tag) {
            $this->publishTag($tag);
        }

        $this->info('Publishing complete.');

        $this->postHandleHook();

        return 0;
    }

    /**
     * Optionally run an action after the main command is finished.
     */
    protected function postHandleHook()
    {
        //
    }

        /**
     * Determine the provider or tag(s) to publish.
     *
     * @return void
     */
    protected function determineWhatShouldBePublished()
    {
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

        $this->parseChoice($choice);
    }

    /**
     * Parse the answer that was given via the prompt.
     *
     * @param  string  $choice
     * @return void
     */
    protected function parseChoice($choice)
    {
        [$type, $value] = explode(': ', strip_tags($choice));

        if ($type === 'Provider') {
            $this->provider = $value;
        } elseif ($type === 'Tag') {
            $this->tags = [$value];
        }
    }
    
    /**
     * Publishes the assets for a tag.
     *
     * @param  string  $tag
     * @return int
     */
    protected function publishTag($tag): int
    {
        $published = false;

        $pathsToPublish = $this->pathsToPublish($tag);

        foreach ($pathsToPublish as $from => $to) {
            $this->publishItem($from, $to);

            $published = true;
        }

        if ($published === false) {
            $this->comment('No publishable resources for tag ['.$tag.'].');
        } else {
            $this->laravel['events']->dispatch(new VendorTagPublished($tag, $pathsToPublish));
        }

        return 0;
    }

      /**
     * Get all the paths to publish.
     *
     * @param  string  $tag
     * @return array
     */
    protected function pathsToPublish($tag)
    {
        return ServiceProvider::pathsToPublish(
            $this->provider,
            $tag
        );
    }


    /**
     * Publish the given item from and to the given location.
     *
     * @param  string  $from
     * @param  string  $to
     * @return void
     */
    protected function publishItem($from, $to)
    {
        if ($this->files->isFile($from)) {
            return $this->publishFile($from, $to);
        } elseif ($this->files->isDirectory($from)) {
            return $this->publishDirectory($from, $to);
        }

        $this->error("Can't locate path: <{$from}>");
    }

    /**
     * Publish the file to the given path.
     *
     * @param  string  $from
     * @param  string  $to
     * @return void
     */
    protected function publishFile($from, $to)
    {
        if (! $this->files->exists($to) || $this->option('force')) {
            $this->createParentDirectory(dirname($to));

            $this->files->copy($from, $to);

            $this->status($from, $to, 'File');
        }
    }

    /**
     * Publish the directory to the given directory.
     *
     * @param  string  $from
     * @param  string  $to
     * @return void
     */
    protected function publishDirectory($from, $to)
    {
        $visibility = PortableVisibilityConverter::fromArray([], Visibility::PUBLIC);

        $this->moveManagedFiles(new MountManager([
            'from' => new Flysystem(new LocalAdapter($from)),
            'to' => new Flysystem(new LocalAdapter($to, $visibility)),
        ]));

        $this->status($from, $to, 'Directory');
    }

    /**
     * Move all the files in the given MountManager.
     *
     * @param  \League\Flysystem\MountManager  $manager
     * @return void
     */
    protected function moveManagedFiles($manager)
    {
        foreach ($manager->listContents('from://', true) as $file) {
            $path = Str::after($file['path'], 'from://');

            if ($file['type'] === 'file' && (! $manager->fileExists('to://'.$path) || $this->option('force'))) {
                $manager->write('to://'.$path, $manager->read($file['path']));
            }
        }
    }

    /**
     * Create the directory to house the published files if needed.
     *
     * @param  string  $directory
     * @return void
     */
    protected function createParentDirectory($directory)
    {
        if (! $this->files->isDirectory($directory)) {
            $this->files->makeDirectory($directory, 0755, true);
        }
    }

    /**
     * Write a status message to the console.
     *
     * @param  string  $from
     * @param  string  $to
     * @param  string  $type
     * @return void
     */
    protected function status($from, $to, $type)
    {
        $from = str_replace(base_path(), '', realpath($from));

        $to = str_replace(base_path(), '', realpath($to));

        $this->line('<info>Copied '.$type.'</info> <comment>['.$from.']</comment> <info>To</info> <comment>['.$to.']</comment>');
    }
}
