<?php

namespace Hyde\Framework\Commands;

use Exception;
use Hyde\Framework\Actions\CreatesDefaultDirectories;
use Hyde\Framework\Concerns\Internal\BuildActionRunner;
use Hyde\Framework\Concerns\Internal\TransfersMediaAssetsForBuildCommands;
use Hyde\Framework\Features;
use Hyde\Framework\Hyde;
use Hyde\Framework\Models\BladePage;
use Hyde\Framework\Models\DocumentationPage;
use Hyde\Framework\Models\MarkdownPage;
use Hyde\Framework\Models\MarkdownPost;
use Hyde\Framework\Services\DiscoveryService;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use LaravelZero\Framework\Commands\Command;

/**
 * Hyde Command to run the Build Process.
 *
 * @see \Tests\Feature\Commands\BuildStaticSiteCommandTest
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
        {--run-prettier : Should the build files be prettified?}
        {--pretty-urls : Should links in output use pretty URLs?}
        {--no-api : Disable external API calls, such as Torchlight}';

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

        $this->printInitialInformation();

        $this->purge();

        $this->transferMediaAssets();

        if (Features::hasBlogPosts()) {
            $this->runBuildAction(MarkdownPost::class);
        }

        if (Features::hasMarkdownPages()) {
            $this->runBuildAction(MarkdownPage::class);
        }

        if (Features::hasDocumentationPages()) {
            $this->runBuildAction(DocumentationPage::class);
        }

        if (Features::hasBladePages()) {
            $this->runBuildAction(BladePage::class);
        }

        $this->postBuildActions();

        $this->printFinishMessage($time_start);

        return 0;
    }

    /** @internal */
    protected function printInitialInformation(): void
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
            Config::set(['hyde.prettyUrls' => true]);
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

    /**
     * Run any post-build actions.
     *
     * @todo #363 Add unit test for prettier command (can work by comparing compiled baseline to output to see if it was prettified);
     *
     * @return void
     */
    public function postBuildActions(): void
    {
        if ($this->option('run-prettier')) {
            $this->runNodeCommand(
                'npx prettier '.Hyde::pathToRelative(Hyde::getSiteOutputPath($path)).'/ --write --bracket-same-line',
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
}
