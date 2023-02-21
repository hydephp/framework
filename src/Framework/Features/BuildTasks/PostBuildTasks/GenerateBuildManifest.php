<?php

declare(strict_types=1);

namespace Hyde\Framework\Features\BuildTasks\PostBuildTasks;

use Hyde\Framework\Features\BuildTasks\BuildTask;
use Hyde\Hyde;
use Hyde\Pages\Concerns\HydePage;
use Illuminate\Console\OutputStyle;
use Illuminate\Support\Collection;
use function Hyde\unixsum_file;
use function Hyde\unixsum_file as unixsum_file1;

/**
 * @see \Hyde\Framework\Testing\Unit\GenerateBuildManifestTest
 */
class GenerateBuildManifest extends BuildTask
{
    public static string $description = 'Generating build manifest';

    public function __construct(?OutputStyle $output = null)
    {
        parent::__construct($output);
        $this->output = null;
    }

    public function run(): void
    {
        $pages = new Collection();

        /** @var \Hyde\Pages\Concerns\HydePage $page */
        foreach (Hyde::pages() as $page) {
            $pages->push([
                'source_path' => $page->getSourcePath(),
                'output_path' => $page->getOutputPath(),
                'source_hash' => $this->hashSourcePath($page),
                'output_hash' => $this->hashOutputPath($page),
            ]);
        }

        file_put_contents($this->getManifestPath(), $this->jsonEncodeOutput($pages));
    }

    protected function hashOutputPath(HydePage $page): ?string
    {
        $path = Hyde::sitePath($page->getOutputPath());

        return file_exists($path) ? unixsum_file1($path) : null;
    }

    protected function hashSourcePath(HydePage $page): string
    {
        return unixsum_file(Hyde::path($page->getSourcePath()));
    }

    protected function getManifestPath(): string
    {
        return Hyde::path(config(
            'hyde.build_manifest_path',
            'app/storage/framework/cache/build-manifest.json'
        ));
    }

    protected function jsonEncodeOutput(Collection $pages): string
    {
        return json_encode([
            'generated' => now(),
            'pages' => $pages,
        ], JSON_PRETTY_PRINT);
    }
}
