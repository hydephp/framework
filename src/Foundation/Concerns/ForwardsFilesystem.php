<?php

namespace Hyde\Framework\Foundation\Concerns;

/**
 * @internal Single-use trait for the HydeKernel class.
 *
 * @see \Hyde\Framework\HydeKernel
 */
trait ForwardsFilesystem
{
    public function path(string $path = ''): string
    {
        return $this->filesystem->path($path);
    }

    public function vendorPath(string $path = ''): string
    {
        return $this->filesystem->vendorPath($path);
    }

    public function copy(string $from, string $to): bool
    {
        return $this->filesystem->copy($from, $to);
    }

    public function touch(string|array $path): bool
    {
        return $this->filesystem->touch($path);
    }

    public function unlink(string|array $path): bool
    {
        return $this->filesystem->unlink($path);
    }

    public function getModelSourcePath(string $model, string $path = ''): string
    {
        return $this->filesystem->getModelSourcePath($model, $path);
    }

    public function getBladePagePath(string $path = ''): string
    {
        return $this->filesystem->getBladePagePath($path);
    }

    public function getMarkdownPagePath(string $path = ''): string
    {
        return $this->filesystem->getMarkdownPagePath($path);
    }

    public function getMarkdownPostPath(string $path = ''): string
    {
        return $this->filesystem->getMarkdownPostPath($path);
    }

    public function getDocumentationPagePath(string $path = ''): string
    {
        return $this->filesystem->getDocumentationPagePath($path);
    }

    public function getSiteOutputPath(string $path = ''): string
    {
        return $this->filesystem->getSiteOutputPath($path);
    }

    public function pathToRelative(string $path): string
    {
        return $this->filesystem->pathToRelative($path);
    }
}
