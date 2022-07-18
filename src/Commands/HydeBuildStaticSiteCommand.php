<?php

namespace Hyde\Framework\Commands;

use Exception;
use Hyde\Framework\Actions\PostBuildTasks\GenerateSitemap;
use Hyde\Framework\Helpers\Features;
use Hyde\Framework\Hyde;
use Hyde\Framework\Services\BuildHookService;
use Hyde\Framework\Services\BuildService;
use Hyde\Framework\Services\CollectionService;
use Hyde\Framework\Services\DiscoveryService;
use Hyde\Framework\Services\RssFeedService;
use Hyde\Framework\Services\SitemapService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use LaravelZero\Framework\Commands\Command;

/**
 * Hyde Command to run the Build Process.
 *
 * @see \Hyde\Framework\Testing\Feature\StaticSiteServiceTest
 */
class HydeBuildStaticSiteCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'build 
        {--run-dev : Run the NPM dev script after build}
        {--run-prod : Run the NPM prod script after build}
        {--run-prettier : Format the output using NPM Prettier}
        {--pretty-urls : Should links in output use pretty URLs?}
        {--no-api : Disable API calls, for example, Torchlight}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Build the static site';

    protected BuildService $service;

    /**
     * Execute the console command.
     *
     * @return int
     *
     * @throws Exception
     */
    public function handle(): int
    {
        $time_start = microtime(true);

        $this->title('Building your static site!');

        $this->service = new BuildService($this->output);

        $this->runPreBuildActions();

        $this->service->cleanOutputDirectory();

        $this->service->transferMediaAssets();

        $this->service->compileStaticPages();

        $this->runPostBuildActions();

        $this->printFinishMessage($time_start);

        return 0;
    }

    /** @internal */
    protected function runPreBuildActions(): void
    {
        if ($this->option('no-api')) {
            $this->info('Disabling external API calls');
            $this->newLine();
            $config = config('hyde.features');
            unset($config[array_search('torchlight', $config)]);
            Config::set(['hyde.features' => $config]);
        }

        if ($this->option('pretty-urls')) {
            $this->info('Generating site with pretty URLs');
            $this->newLine();
            Config::set(['site.pretty_urls' => true]);
        }
    }

    /**
     * Run any post-build actions.
     *
     * @return void
     */
    public function runPostBuildActions(): void
    {
        $service = new BuildHookService($this->output);

        if ($this->option('run-prettier')) {
            $this->runNodeCommand(
                'npx prettier '.Hyde::pathToRelative(Hyde::getSiteOutputPath()).'/**/*.html --write --bracket-same-line',
                'Prettifying code!',
                'prettify code'
            );
        }

        if ($this->option('run-dev')) {
            $this->runNodeCommand('npm run dev', 'Building frontend assets for development!');
        }

        if ($this->option('run-prod')) {
            $this->runNodeCommand('npm run prod', 'Building frontend assets for production!');
        }

        $service->runIf(GenerateSitemap::class, $this->canGenerateSitemap());

        if ($this->canGenerateFeed()) {
            Artisan::call('build:rss', outputBuffer: $this->output);
        }

        if ($this->canGenerateSearch()) {
            Artisan::call('build:search', outputBuffer: $this->output);
        }

        $service->runPostBuildTasks();
    }

    /** @internal */
    protected function printFinishMessage(float $time_start): void
    {
        $execution_time = (microtime(true) - $time_start);
        $this->info(sprintf('All done! Finished in %s seconds. (%sms)',
            number_format($execution_time, 2), number_format($execution_time * 1000, 2)
        ));

        $this->info('Congratulations! 🎉 Your static site has been built!');
        $this->line(
            'Your new homepage is stored here -> '.
            DiscoveryService::createClickableFilepath(Hyde::getSiteOutputPath('index.html'))
        );
    }

    /* @internal */
    protected function runNodeCommand(string $command, string $message, ?string $actionMessage = null): void
    {
        $this->info($message.' This may take a second.');

        $output = shell_exec(sprintf('%s%s',
            app()->environment() === 'testing' ? 'echo ' : '',
            $command)
        );

        $this->line($output ?? sprintf(
            '<fg=red>Could not %s! Is NPM installed?</>',
            $actionMessage ?? 'run script'
        ));
    }

    protected function canGenerateSitemap(): bool
    {
        return SitemapService::canGenerateSitemap();
    }

    protected function canGenerateFeed(): bool
    {
        return RssFeedService::canGenerateFeed()
            && count(CollectionService::getMarkdownPostFiles()) > 0;
    }

    protected function canGenerateSearch(): bool
    {
        return Features::hasDocumentationSearch()
            && count(CollectionService::getDocumentationPageFiles()) > 0;
    }
}
