<?php

namespace Hyde\Framework\Actions\PostBuildTasks;

use Hyde\Framework\Concerns\AbstractBuildTask;
use Hyde\Framework\Concerns\HydePage;
use Hyde\Framework\Hyde;
use Hyde\Framework\Services\ViewDiffService;
use Illuminate\Console\OutputStyle;
use Illuminate\Support\Collection;

/**
 * @see \Hyde\Framework\Testing\Unit\GenerateBuildManifestTest
 */
class GenerateBuildManifest extends AbstractBuildTask
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

        /** @var \Hyde\Framework\Concerns\HydePage $page */
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

        return file_exists($path) ? ViewDiffService::unixsumFile($path) : null;
    }

    protected function hashSourcePath(HydePage $page): string
    {
        return ViewDiffService::unixsumFile(Hyde::path($page->getSourcePath()));
    }

    protected function getManifestPath(): string
    {
        return Hyde::path(config(
            'hyde.build_manifest_path',
            'storage/framework/cache/build-manifest.json'
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
