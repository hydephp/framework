<?php

namespace Hyde\Framework\Commands;

use Hyde\Framework\Hyde;
use Hyde\Framework\Services\SitemapService;
use LaravelZero\Framework\Commands\Command;

/**
 * Hyde Command to run the Build Process for the Sitemap.
 *
 * @see \Tests\Feature\Commands\HydeBuildSitemapCommandTest
 */
class HydeBuildSitemapCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'build:sitemap';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Generate the sitemap.xml';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $actionTime = microtime(true);
        
        $this->comment('Generating sitemap...');
        file_put_contents(Hyde::getSiteOutputPath('sitemap.xml'), SitemapService::generateSitemap());
        $this->line(' > Created <info>sitemap.xml</> in '.$this->getExecutionTimeInMs($actionTime)."ms\n");
      
        return 0;
    }

	protected function getExecutionTimeInMs(float $timeStart): float
    {
        return number_format(((microtime(true) - $timeStart) * 1000), 2);
    }
}
