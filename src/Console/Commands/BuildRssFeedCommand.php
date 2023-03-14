<?php

declare(strict_types=1);

namespace Hyde\Console\Commands;

use Hyde\Framework\Actions\PostBuildTasks\GenerateRssFeed;
use LaravelZero\Framework\Commands\Command;

/**
 * Hyde command to run the build process for the RSS feed.
 */
class BuildRssFeedCommand extends Command
{
    /** @var string */
    protected $signature = 'build:rss';

    /** @var string */
    protected $description = 'Generate the RSS feed';

    public function handle(): int
    {
        return (new GenerateRssFeed())->run($this->output);
    }
}
