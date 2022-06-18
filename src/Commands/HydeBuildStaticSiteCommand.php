<?php

namespace Hyde\Framework\Commands;

use Exception;
use Hyde\Framework\Actions\CreatesDefaultDirectories;
use Hyde\Framework\Concerns\Internal\BuildActionRunner;
use Hyde\Framework\Concerns\Internal\TransfersMediaAssetsForBuildCommands;
use Hyde\Framework\Helpers\Features;
use Hyde\Framework\Hyde;
use Hyde\Framework\Models\BladePage;
use Hyde\Framework\Models\DocumentationPage;
use Hyde\Framework\Models\MarkdownPage;
use Hyde\Framework\Models\MarkdownPost;
use Hyde\Framework\Services\CollectionService;
use Hyde\Framework\Services\DiscoveryService;
use Hyde\Framework\Services\RssFeedService;
use Hyde\Framework\Services\SitemapService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use LaravelZero\Framework\Commands\Command;

/**
 * Hyde Command to run the Build Process.
 *
 * @see \Hyde\Testing\Framework\Feature\Commands\BuildStaticSiteCommandTest
 */
class HydeBuildStaticSiteCommand extends Command
{
    use BuildActionRunner;
    use TransfersMediaAssetsForBuildCommands;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'build 
        {--run-dev : Run the NPM dev script after build}
        {--run-prod : Run the NPM prod script after build}
        {--pretty : Deprecated option, use --run-prettier instead}
        {--run-prettier : Format the output using NPM Prettier}
        {--pretty-urls : Should links in output use pretty URLs?}
        {--no-api : Disable API calls, for example, Torchlight}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Build the static site';

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

        $this->runPreBuildActions();

        $this->purge();

        $this->transferMediaAssets();

        if (Features::hasBladePages()) {
            $this->runBuildAction(BladePage::class);
        }

        if (Features::hasMarkdownPages()) {
            $this->runBuildAction(MarkdownPage::class);
        }

        if (Features::hasBlogPosts()) {
            $this->runBuildAction(MarkdownPost::class);
        }

        if (Features::hasDocumentationPages()) {
            $this->runBuildAction(DocumentationPage::class);
        }

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
            Config::set(['hyde.pretty_urls' => true]);
        }
    }

    /**
     * Run any post-build actions.
     *
     * @return void
     */
    public function runPostBuildActions(): void
    {
        if ($this->option('run-prettier') || $this->option('pretty')) {
            if ($this->option('pretty')) {
                $this->warn('<error>Warning:</> The --pretty option is deprecated, use --run-prettier instead');
            }
            $this->runNodeCommand(
                'npx prettier '.Hyde::pathToRelative(Hyde::getSiteOutputPath()).'/ --write --bracket-same-line',
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

        if ($this->canGenerateSitemap()) {
            Artisan::call('build:sitemap', outputBuffer: $this->output);
        }

        if ($this->canGenerateFeed()) {
            Artisan::call('build:rss', outputBuffer: $this->output);
        }

        if ($this->canGenerateSearch()) {
            Artisan::call('build:search', outputBuffer: $this->output);
        }
    }

    /** @internal */
    protected function printFinishMessage(float $time_start): void
    {
        $time_end = microtime(true);
        $execution_time = ($time_end - $time_start);
        $this->info('All done! Finished in '.number_format(
            $execution_time,
            2
        ).' seconds. ('.number_format(($execution_time * 1000), 2).'ms)');

        $this->info('Congratulations! 🎉 Your static site has been built!');
        $this->line(
            'Your new homepage is stored here -> '.
            DiscoveryService::createClickableFilepath(Hyde::getSiteOutputPath('index.html'))
        );
    }

    /**
     * Clear the entire output directory before running the build.
     *
     * @return void
     */
    public function purge(): void
    {
        $this->warn('Removing all files from build directory.');

        File::deleteDirectory(Hyde::getSiteOutputPath());
        mkdir(Hyde::getSiteOutputPath());

        $this->line('<fg=gray> > Directory purged');

        $this->line(' > Recreating directories');
        (new CreatesDefaultDirectories)->__invoke();

        $this->line('</>');
    }

    /** @internal */
    protected function getModelPluralName(string $model): string
    {
        return preg_replace('/([a-z])([A-Z])/', '$1 $2', class_basename($model)).'s';
    }

    /* @internal */
    private function runNodeCommand(string $command, string $message, ?string $actionMessage = null): void
    {
        $this->info($message.' This may take a second.');

        if (app()->environment() === 'testing') {
            $command = 'echo '.$command;
        }
        $output = shell_exec($command);

        $this->line(
            $output ?? '<fg=red>Could not '.($actionMessage ?? 'run script').'! Is NPM installed?</>'
        );
    }

    protected function canGenerateSitemap(): bool
    {
        return SitemapService::canGenerateSitemap();
    }

    protected function canGenerateFeed(): bool
    {
        return RssFeedService::canGenerateFeed()
            && count(CollectionService::getMarkdownPostList()) > 0;
    }

    protected function canGenerateSearch(): bool
    {
        return Features::hasDocumentationSearch()
            && count(CollectionService::getDocumentationPageList()) > 0;
    }
}
