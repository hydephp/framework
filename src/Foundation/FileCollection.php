<?php

namespace Hyde\Framework\Foundation;

use Hyde\Framework\Contracts\AbstractPage;
use Hyde\Framework\Foundation\Concerns\BaseFoundationCollection;
use Hyde\Framework\Helpers\Features;
use Hyde\Framework\Models\File;
use Hyde\Framework\Models\Pages\BladePage;
use Hyde\Framework\Models\Pages\DocumentationPage;
use Hyde\Framework\Models\Pages\MarkdownPage;
use Hyde\Framework\Models\Pages\MarkdownPost;
use Hyde\Framework\Services\DiscoveryService;

/**
 * @see \Hyde\Framework\Foundation\FileCollection
 */
final class FileCollection extends BaseFoundationCollection
{
    public function getSourceFiles(?string $pageClass = null): self
    {
        return ! $pageClass ? $this->getAllSourceFiles() : $this->getSourceFilesFor($pageClass);
    }

    public function getAllSourceFiles(): self
    {
        return $this->filter(function (File $file) {
            return $file->belongsTo !== null;
        });
    }

    public function getSourceFilesFor(string $pageClass): self
    {
        return $this->filter(function (File $file) use ($pageClass): bool {
            return $file->belongsTo() === $pageClass;
        });
    }

    public function getMediaFiles(): self
    {
        return $this->filter(function (File $file): bool {
            return str_starts_with($file, '_media');
        });
    }

    protected function runDiscovery(): self
    {
        if (Features::hasBladePages()) {
            $this->discoverFilesFor(BladePage::class);
        }

        if (Features::hasMarkdownPages()) {
            $this->discoverFilesFor(MarkdownPage::class);
        }

        if (Features::hasBlogPosts()) {
            $this->discoverFilesFor(MarkdownPost::class);
        }

        if (Features::hasDocumentationPages()) {
            $this->discoverFilesFor(DocumentationPage::class);
        }

        $this->discoverMediaAssetFiles();

        return $this;
    }

    /** @param string<AbstractPage> $pageClass */
    protected function discoverFilesFor(string $pageClass): void
    {
        // Scan the source directory, and directories therein, for files that match the model's file extension.
        foreach (glob($this->kernel->path($pageClass::qualifyBasename('{*,**/*}')), GLOB_BRACE) as $filepath) {
            if (! str_starts_with(basename($filepath), '_')) {
                $this->put($this->kernel->pathToRelative($filepath), File::make($filepath)->belongsTo($pageClass));
            }
        }
    }

    protected function discoverMediaAssetFiles(): void
    {
        foreach (DiscoveryService::getMediaAssetFiles() as $filepath) {
            $this->put($this->kernel->pathToRelative($filepath), File::make($filepath));
        }
    }
}
