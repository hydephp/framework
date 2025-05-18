<?php

declare(strict_types=1);

namespace Hyde\Framework\Actions\PreBuildTasks;

use Hyde\Facades\Config;
use Hyde\Facades\Filesystem;
use Hyde\Support\Filesystem\MediaFile;
use Hyde\Framework\Features\BuildTasks\PreBuildTask;
use Hyde\Framework\Concerns\InteractsWithDirectories;

class TransferMediaAssets extends PreBuildTask
{
    protected static string $message = 'Transferring Media Assets';

    use InteractsWithDirectories;

    public function handle(): void
    {
        $this->newLine();

        $files = MediaFile::all();

        if (Config::getBool('hyde.load_app_styles_from_cdn', false)) {
            $files->forget('app.css');
        }

        if ($files->isEmpty()) {
            $this->skip("No media files to transfer.\n");
        }

        $this->withProgressBar($files, function (MediaFile $file): void {
            $sitePath = $file->getOutputPath();
            $this->needsParentDirectory($sitePath);
            Filesystem::putContents($sitePath, $file->getContents());
        });

        $this->newLine();
    }

    public function printFinishMessage(): void
    {
        // We don't need a finish message for this task.
    }
}
