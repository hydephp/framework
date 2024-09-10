<?php

declare(strict_types=1);

namespace Hyde\Foundation\Kernel;

use Hyde\Facades\Filesystem;
use Hyde\Foundation\Concerns\BaseFoundationCollection;
use Hyde\Framework\Exceptions\FileNotFoundException;
use Hyde\Pages\Concerns\HydePage;
use Hyde\Support\Filesystem\SourceFile;

use function basename;
use function str_starts_with;

/**
 * The FileCollection contains all the discovered source files.
 *
 * @template T of \Hyde\Support\Filesystem\SourceFile
 *
 * @extends \Hyde\Foundation\Concerns\BaseFoundationCollection<string, T>
 *
 * @property array<string, SourceFile> $items The files in the collection.
 *
 * @method SourceFile|null get(string $key, SourceFile $default = null)
 *
 * This class is stored as a singleton in the HydeKernel.
 * You would commonly access it via the facade or Hyde helper:
 *
 * @see \Hyde\Foundation\Facades\Files
 * @see \Hyde\Hyde::files()
 */
final class FileCollection extends BaseFoundationCollection
{
    public function addFile(SourceFile $file): void
    {
        $this->put($file->getPath(), $file);
    }

    protected function runDiscovery(): void
    {
        /** @var class-string<\Hyde\Pages\Concerns\HydePage> $pageClass */
        foreach ($this->kernel->getRegisteredPageClasses() as $pageClass) {
            if ($pageClass::isDiscoverable()) {
                $this->discoverFilesFor($pageClass);
            }
        }
    }

    protected function runExtensionHandlers(): void
    {
        /** @var class-string<\Hyde\Foundation\Concerns\HydeExtension> $extension */
        foreach ($this->kernel->getExtensions() as $extension) {
            $extension->discoverFiles($this);
        }
    }

    /** @param class-string<HydePage> $pageClass */
    protected function discoverFilesFor(string $pageClass): void
    {
        // Scan the source directory, and directories therein, for files that match the model's file extension.
        foreach (Filesystem::smartGlob($pageClass::sourcePath('{*,**/*}'), GLOB_BRACE) as $path) {
            if (! str_starts_with(basename((string) $path), '_')) {
                $this->addFile(SourceFile::make($path, $pageClass));
            }
        }
    }

    public function getFile(string $path): SourceFile
    {
        return $this->get($path) ?? throw new FileNotFoundException($path);
    }

    /** @param  class-string<\Hyde\Pages\Concerns\HydePage>|null  $pageClass */
    public function getFiles(?string $pageClass = null): FileCollection
    {
        return $pageClass ? $this->filter(function (SourceFile $file) use ($pageClass): bool {
            return $file->pageClass === $pageClass;
        }) : $this;
    }
}
