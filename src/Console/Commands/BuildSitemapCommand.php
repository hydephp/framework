<?php

declare(strict_types=1);

namespace Hyde\Console\Commands;

use Hyde\Framework\Features\BuildTasks\PostBuildTasks\GenerateSitemap;
use LaravelZero\Framework\Commands\Command;

/**
 * Hyde command to run the build process for the sitemap.
 *
 * @see \Hyde\Framework\Testing\Feature\Commands\BuildSitemapCommandTest
 */
class BuildSitemapCommand extends Command
{
    protected $signature = 'build:sitemap';
    protected $description = 'Generate the sitemap.xml';

    public function handle(): int
    {
        return (new GenerateSitemap($this->output))->handle() ?? Command::SUCCESS;
    }
}
