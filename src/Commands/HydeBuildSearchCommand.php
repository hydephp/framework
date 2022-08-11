<?php

namespace Hyde\Framework\Commands;

use Hyde\Framework\Actions\GeneratesDocumentationSearchIndexFile;
use Hyde\Framework\Concerns\ActionCommand;
use Hyde\Framework\Hyde;
use Hyde\Framework\Services\DiscoveryService;

/**
 * Hyde Command to run the Build Process for the DocumentationSearchIndex.
 *
 * @todo Add configuration option to enable/disable this feature.
 *
 * @see \Hyde\Framework\Testing\Feature\Commands\HydeBuildSearchCommandTest
 */
class HydeBuildSearchCommand extends ActionCommand
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
        $this->action('Generating documentation site search index', function () {
            $expected = $this->guesstimateGenerationTime();
            if ($expected > 0) {
                $this->line("<fg=gray> > This will take an estimated $expected seconds. Terminal may seem non-responsive.</>");
            }

            GeneratesDocumentationSearchIndexFile::run();
        }, sprintf('Created <info>%s</info>', GeneratesDocumentationSearchIndexFile::$filePath));

        if (config('docs.create_search_page', true)) {
            $this->action('Generating search page', function () {
                file_put_contents(
                    Hyde::path(sprintf(
                        '_site/%s/search.html',
                        config('docs.output_directory', 'docs')
                    )),
                    view('hyde::pages.documentation-search')->render()
                );
            }, sprintf(
                'Created <info>_site/%s/search.html</info>',
                config('docs.output_directory', 'docs')
            ));
        }

        return 0;
    }

    /** @internal Estimated processing time per file in ms */
    public static float $guesstimationFactor = 52.5;

    protected function guesstimateGenerationTime(): int|float
    {
        return (int) round(count(DiscoveryService::getDocumentationPageFiles()) * static::$guesstimationFactor) / 1000;
    }
}
