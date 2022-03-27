<?php

namespace Hyde\Framework\Commands;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;

/**
 * Publish the Hyde Config Files.
 *
 * @uses BasePublishingCommand
 */
class HydePublishConfigsCommand extends BasePublishingCommand
{
    protected $signature = 'publish:configs {--force : Overwrite any existing files}';

    protected $description = 'Publish the Hyde configuration files';

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
     *
     * @throws \League\Flysystem\FilesystemException
     */
    public function handle(): int
    {
        $this->tags = array_flip(array_filter(
            array_flip(ServiceProvider::publishableGroups()),
            fn ($key) => str_starts_with($key, 'configs'),
            ARRAY_FILTER_USE_KEY
        ));

        foreach ($this->tags ?: [null] as $tag) {
            $this->publishTag($tag);
        }

        $this->info('Publishing complete.');

        return 0;
    }

    /**
     * Determine the provider or tag(s) to publish.
     *
     * @return void
     */
    protected function determineWhatShouldBePublished()
    {

    }

    /**
     * Prompt for which tag to publish.
     *
     * @return void
     */
    protected function promptForProviderOrTag()
    {

    }

    /**
     * The choices available via the prompt.
     *
     * @return array
     */
    protected function publishableChoices(): array
    {
        return [];
    }
}
