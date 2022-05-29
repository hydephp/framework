<?php

namespace Hyde\Framework\Commands;

use Hyde\Framework\Actions\GeneratesDocumentationSearchIndexFile;
use Hyde\Framework\Hyde;
use Hyde\Framework\Models\DocumentationPage;
use LaravelZero\Framework\Commands\Command;

/**
 * Hyde Command to run the Build Process for the DocumentationSearchIndex.
 *
 * @todo Add configuration option to enable/disable this feature.
 *
 * @see \Tests\Feature\Commands\HydeBuildSearchCommandTest
 */
class HydeBuildSearchCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'build:search';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Generate the docs/search.json';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $actionTime = microtime(true);

        $this->comment('Generating documentation site search index...');
        GeneratesDocumentationSearchIndexFile::run();
        $this->line(' > Created <info>'.GeneratesDocumentationSearchIndexFile::$filePath.'</> in '.
            $this->getExecutionTimeInMs($actionTime)."ms\n");

        $actionTime = microtime(true);
        $this->comment('Generating search page...');

        // @todo Add Blade support for Documentation pages, and add this
        // as a publishable template. (Or add support for using Blade snippets in Markdown)
        file_put_contents(Hyde::path('_site/docs/search.html'), 
            view('hyde::layouts/docs')->with([
                'page' => new DocumentationPage([], '', 'Search', 'search'),
                'title' => 'Search',
                'markdown' => 
'<h1>Search the documentation site</h1>
<style>#searchMenuButton{display:none;}</style>
' .  view('hyde::components.docs.search-component')->render(),
                'currentPage' => 'docs/search',
            ])->render()
        );

        return 0;
    }

    protected function getExecutionTimeInMs(float $timeStart): string
    {
        return number_format(((microtime(true) - $timeStart) * 1000), 2);
    }
}
