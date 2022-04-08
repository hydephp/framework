<?php

namespace Hyde\Framework\Commands;

use Exception;
use Hyde\Framework\Actions\CreatesDefaultDirectories;
use Hyde\Framework\Commands\Traits\RunsNodeCommands;
use Hyde\Framework\Features;
use Hyde\Framework\Hyde;
use Hyde\Framework\Models\BladePage;
use Hyde\Framework\Models\DocumentationPage;
use Hyde\Framework\Models\MarkdownPage;
use Hyde\Framework\Models\MarkdownPost;
use Hyde\Framework\Services\BuildService;
use Hyde\Framework\Services\CollectionService;
use Hyde\Framework\StaticPageBuilder;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use LaravelZero\Framework\Commands\Command;

/**
 * Hyde Command to run the Build Process.
 */
class HydeBuildStaticSiteCommand extends Command
{
    use RunsNodeCommands;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'build 
        {--run-dev : Run the NPM dev script after build}
        {--run-prod : Run the NPM prod script after build}
        {--pretty : Should the build files be prettified?}
        {--clean : Should the output directory be emptied before building?}
        {--force : Allow file deletions when using --clean without confirmation?}
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

        if ($this->handleCleanOption() !== 0) {
            return 1;
        }

        $collection = CollectionService::getMediaAssetFiles();
        if ($this->canRunBuildAction($collection, 'Media Assets', 'Transferring')) {
            $this->withProgressBar(
                $collection,
                function ($filepath) {
                    if ($this->getOutput()->isVeryVerbose()) {
                        $this->line(' > Copying media file '
                            . basename($filepath) . ' to the output media directory');
                    }
                    copy($filepath, Hyde::path('_site/media/' . basename($filepath)));
                }
            );
            $this->newLine(2);
        }

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

    protected function runBuildAction(string $model)
    {
        $collection = CollectionService::getSourceFileListForModel($model);
        $modelName = $this->getModelPluralName($model);
        if ($this->canRunBuildAction($collection, $modelName)) {
            $this->withProgressBar(
                $collection,
                function ($basename) use ($model) {
                    new StaticPageBuilder(
                        BuildService::getParserInstanceForModel(
                            $model,
                            $basename
                        )->get(),
                        true
                    );
                }
            );
            $this->newLine(2);
        }
    }

    /** @internal */
    protected function printInitialInformation(): void
    {
        if ($this->option('no-api')) {
            $this->info('Disabling external API calls');
            $config = config('hyde.features');
            unset($config[array_search('torchlight', $config)]);
            Config::set(['hyde.features' => $config]);
        }
    }

    /** @internal */
    protected function printFinishMessage(float $time_start): void
    {
        $time_end = microtime(true);
        $execution_time = ($time_end - $time_start);
        $this->info('All done! Finished in ' . number_format(
            $execution_time,
            2
        ) . ' seconds. (' . number_format(($execution_time * 1000), 2) . 'ms)');

        $this->info('Congratulations! 🎉 Your static site has been built!');
        $this->line(
            'Your new homepage is stored here -> ' .
                BuildService::createClickableFilepath(Hyde::path('_site/index.html'))
        );
    }

    /** @internal */
    protected function handleCleanOption(): int
    {
        if ($this->option('clean')) {
            if ($this->option('force')) {
                $this->purge();
            } else {
                $this->warn('The --clean option will remove all files in the output directory before building.');
                if ($this->confirm(' Are you sure?')) {
                    $this->purge();
                } else {
                    $this->warn('Aborting.');

                    return 1;
                }
            }
        }
        return 0;
    }

    /**
     * Clear the entire _site directory before running the build.
     *
     * @internal
     * @return void
     */
    public function purge()
    {
        $this->warn('Removing all files from build directory.');

        File::deleteDirectory(Hyde::path('_site'));
        mkdir(Hyde::path('_site'));

        $this->line('<fg=gray> > Directory purged');

        $this->line(' > Recreating directories');
        (new CreatesDefaultDirectories)->__invoke();

        $this->line('</>');
    }

    /**
     * Run any post-build actions.
     *
     * @return void
     */
    public function postBuildActions()
    {
        if ($this->option('pretty')) {
            $this->runNodeCommand('pretty', 'Prettifying code!', 'prettify code');
        }

        if ($this->option('run-dev')) {
            $this->runNodeCommand('dev', 'Building frontend assets for development!');
        }

        if ($this->option('run-prod')) {
            $this->runNodeCommand('prod', 'Building frontend assets for production!');
        }
    }

    /** @internal */
    protected function canRunBuildAction(array $collection, string $name, ?string $verb = null): bool
    {
        if (sizeof($collection) < 1) {
            $this->line('No ' . $name . ' found. Skipping...');
            $this->newLine();
            return false;
        }

        $this->comment(($verb ?? 'Creating') . " $name...");
        return true;
    }

    /** @internal */
    protected function getModelPluralName(string $model): string
    {
        return preg_replace('/([a-z])([A-Z])/', '$1 $2', class_basename($model)) . 's';
    }
}
