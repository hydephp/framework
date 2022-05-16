<?php

namespace Hyde\Framework\Concerns\Internal;

use Hyde\Framework\Hyde;
use Hyde\Framework\Services\CollectionService;
use Hyde\Framework\Concerns\InteractsWithDirectories;

/**
 * Transfer all media assets to the build directory.
 *
 * @see \Hyde\Framework\Commands\HydeBuildStaticSiteCommand
 * @see \Hyde\Framework\Commands\HydeRebuildStaticSiteCommand
 *
 * @internal
 */
trait TransfersMediaAssetsForBuildCommands
{
    use BuildActionRunner;
    use InteractsWithDirectories;

    /** @internal */
    protected function transferMediaAssets(): void
    {
        $this->needsDirectory(Hyde::getSiteOutputPath('media'));

        $collection = CollectionService::getMediaAssetFiles();
        if ($this->canRunBuildAction($collection, 'Media Assets', 'Transferring')) {
            $this->withProgressBar(
                $collection,
                function ($filepath) {
                    copy($filepath, Hyde::getSiteOutputPath('media/'.basename($filepath)));
                }
            );
            $this->newLine(2);
        }
    }
}
