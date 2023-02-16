<?php

declare(strict_types=1);

namespace Hyde\Framework\Concerns;

use Hyde\Facades\Filesystem;

/**
 * @see \Hyde\Framework\Testing\Unit\InteractsWithDirectoriesConcernTest
 */
trait InteractsWithDirectories
{
    /**
     * Ensure the supplied directory exist by creating it if it does not.
     *
     * @param  string  $directory  relative file path to the directory
     */
    public static function needsDirectory(string $directory): void
    {
        if (! Filesystem::exists($directory)) {
            Filesystem::makeDirectory($directory, recursive: true);
        }
    }

    /**
     * Ensure the supplied directories exist by creating them if they don't.
     *
     * @param  array<string>  $directories  array with relative file paths to the directories
     */
    public static function needsDirectories(array $directories): void
    {
        foreach ($directories as $directory) {
            static::needsDirectory($directory);
        }
    }

    /**
     * Ensure the supplied file's parent directory exists by creating it if it does not.
     */
    public static function needsParentDirectory(string $file, int $levels = 1): void
    {
        static::needsDirectory(dirname($file, $levels));
    }
}
