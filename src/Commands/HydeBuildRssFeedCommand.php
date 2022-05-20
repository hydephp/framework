<?php

namespace Hyde\Framework\Commands;

use Hyde\Framework\Hyde;
use Hyde\Framework\Services\RssFeedService;
use LaravelZero\Framework\Commands\Command;

/**
 * Hyde Command to run the Build Process for the RSS Feed.
 *
 * @see \Tests\Feature\Commands\HydeBuildRssFeedCommandTest
 */
class HydeBuildRssFeedCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'build:rss {--no-api : (Not yet supported)}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Generate the RSS feed';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
		$actionTime = microtime(true);
		$this->comment('Generating RSS feed...');
        
        if (! $this->runPreflightCheck()) {
            return 1;
        }

		file_put_contents(Hyde::getSiteOutputPath(RssFeedService::getDefaultOutputFilename()), $this->generateFeed());
		$this->line(' > Created <info>'.RssFeedService::getDefaultOutputFilename().'</> in '.$this->getExecutionTimeInMs($actionTime)."ms\n");
	
        return 0;
    }

	protected function generateFeed(): string
	{
		return (new RssFeedService)->withDebugOutput()->generate()->getXML();
	}

    protected function runPreflightCheck(): bool
    {
        if (! RssFeedService::canGenerateFeed()) {
            $this->error('Cannot generate an RSS feed, please check your configuration.');
            return false;
        }

		if ($this->areThereRemoteImages()) {
			$this->line(' > <comment>Heads up!</> There are remote images in your blog posts.');
			$this->line('             Generating the RSS feed will take a bit longer as HTTP requests need to be made.');
		}

        return true;
    }

	protected function areThereRemoteImages(): bool
	{
		$posts = Hyde::getLatestPosts();

		foreach ($posts as $post) {
			if (isset($post->image)) {
				if (str_starts_with($post->image->getSource(), 'http')) {
					return true;
				}
			}
		}

		return true;
	}

	protected function getExecutionTimeInMs(float $timeStart): float
    {
        return number_format(((microtime(true) - $timeStart) * 1000), 2);
    }
}
