<?php

namespace Hyde\Framework\Commands;

use Exception;
use LaravelZero\Framework\Commands\Command;
use Hyde\Framework\Services\CollectionService;
use Hyde\Framework\DocumentationPageParser;
use Hyde\Framework\Features;
use Hyde\Framework\Hyde;
use Hyde\Framework\MarkdownPostParser;
use Hyde\Framework\MarkdownPageParser;
use Hyde\Framework\StaticPageBuilder;
use Hyde\Framework\Models\BladePage;
use Hyde\Framework\Models\MarkdownPage;
use Hyde\Framework\Models\MarkdownPost;
use Hyde\Framework\Models\DocumentationPage;

class BuildStaticSiteCommand extends Command
{
    /**
    * The signature of the command.
    *
    * @var string
    */
    protected $signature = 'build 
        {--run-dev : Run the NPM dev script after build}
        {--run-prod : Run the NPM prod script after build}
        {--pretty : Should the build files be prettified?}';
        
    /**
    * The description of the command.
    *
    * @var string
    */
    protected $description = 'Build the static site';
    
    
    private function debug(array $output)
    {
        if ($this->getOutput()->isVeryVerbose()) {
            $this->newLine();
            $this->line("<fg=gray>Created {$output['createdFileSize']} byte file {$output['createdFilePath']}</>");
            $this->newLine();
        }
    }
    
    /**
    * Execute the console command.
    *
    * @return int
    * @throws Exception
    */
    public function handle(): int
    {
        $time_start = microtime(true);
        
        $this->title('Building your static site!');
        
        if ($this->getOutput()->isVeryVerbose()) {
            $this->warn('Running with high verbosity');
        }
        
        $collection = glob(Hyde::path('_media/*.{png,svg,jpg,jpeg,gif,ico}'), GLOB_BRACE);
        if (sizeof($collection) < 1) {
            $this->line('No Media Assets found. Skipping...');
                $this->newLine();

        } else {
            $this->comment('Transferring Media Assets...');
            $this->withProgressBar(
                $collection,
                function ($filepath) {
                    if ($this->getOutput()->isVeryVerbose()) {
                        $this->line(' > Copying media file '
                        . basename($filepath). ' to the output media directory');
                    }
                    copy($filepath, Hyde::path('_site/media/'. basename($filepath)));
                }
            );
            $this->newLine(2);
        }
        
        if (Features::hasBlogPosts()) {
            $collection = CollectionService::getSourceSlugsOfModels(MarkdownPost::class);
            if (sizeof($collection) < 1) {
                $this->line('No Markdown Posts found. Skipping...');
                $this->newLine();
            } else {
                $this->comment('Creating Markdown Posts...');
                $this->withProgressBar(
                    $collection,
                    function ($slug) {
                        $this->debug((new StaticPageBuilder((new MarkdownPostParser($slug))->get(), true))
                        ->getDebugOutput());
                    }
                );
                $this->newLine(2);
            }
        }
        
        if (Features::hasMarkdownPages()) {
            $collection = CollectionService::getSourceSlugsOfModels(MarkdownPage::class);
            if (sizeof($collection) < 1) {
                $this->line('No Markdown Pages found. Skipping...');
                $this->newLine();
            } else {
                $this->comment('Creating Markdown Pages...');
                $this->withProgressBar(
                    $collection,
                    function ($slug) {
                        $this->debug((new StaticPageBuilder((new MarkdownPageParser($slug))->get(), true))
                            ->getDebugOutput());
                    }
                );
                $this->newLine(2);
            }
        }
            
        if (Features::hasDocumentationPages()) {
            $collection = CollectionService::getSourceSlugsOfModels(DocumentationPage::class);
            
            if (sizeof($collection) < 1) {
                $this->line('No Documentation Pages found. Skipping...');
                $this->newLine();
            } else {
            $this->comment('Creating Documentation Pages...');
                $this->withProgressBar(
                    $collection,
                    function ($slug) {
                        $this->debug((new StaticPageBuilder((new DocumentationPageParser($slug))->get(), true))
                            ->getDebugOutput());
                    }
                );
                $this->newLine(2);
            }
        }
        
        if (Features::hasBladePages()) {
            $collection = CollectionService::getSourceSlugsOfModels(BladePage::class);
            
            if (sizeof($collection) < 1) {
                $this->line('No Blade Pages found. Skipping...');
                $this->newLine();
            } else {
            $this->comment('Creating Custom Blade Pages...');
                $this->withProgressBar(
                    $collection,
                    function ($slug) {
                        $this->debug((new StaticPageBuilder((new BladePage($slug)), true))
                            ->getDebugOutput());
                    }
                );
                $this->newLine(2);
            }
        }
        
        
        if ($this->option('pretty')) {
            $this->info('Prettifying code! This may take a second.');
            try {
                $this->line(shell_exec('npx prettier _site/ --write'));
            } catch (Exception) {
                $this->warn('Could not prettify code! Is NPM installed?');
            }
        }
        
        if ($this->option('run-dev')) {
            $this->info('Building frontend assets for development! This may take a second.');
            try {
                $this->line(shell_exec('npm run dev'));
            } catch (Exception) {
                $this->warn('Could not run script! Is NPM installed?');
            }
        }
        
        if ($this->option('run-prod')) {
            $this->info('Building frontend assets for production! This may take a second.');
            try {
                $this->line(shell_exec('npm run prod'));
            } catch (Exception) {
                $this->warn('Could not run script! Is NPM installed?');
            }
        }
        
        $time_end = microtime(true);
        $execution_time = ($time_end - $time_start);
        $this->info('All done! Finished in ' . number_format(
            $execution_time,
            2
            ) .' seconds. (' . number_format(($execution_time * 1000), 2) . 'ms)');
            
            $this->info('Congratulations! 🎉 Your static site has been built!');
            echo(
                "Your new homepage is stored here -> file://" . str_replace(
                    '\\',
                    '/',
                    realpath(Hyde::path('_site/index.html'))
                    )
                );
                
                return 0;
            }
        }
        