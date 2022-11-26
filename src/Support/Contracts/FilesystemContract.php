<?php

declare(strict_types=1);

namespace Hyde\Support\Contracts;

/**
 * Interface for the Illuminate Filesystem class, but with static methods and strong types inferred from the PHPDocs.
 *
 * @see \Illuminate\Filesystem\Filesystem
 */
interface FilesystemContract
{
    /**
     * Determine if a file or directory exists.
     *
     * @param  string  $path
     * @return bool
     */
    public static function exists(string $path): bool;

    /**
     * Determine if a file or directory is missing.
     *
     * @param  string  $path
     * @return bool
     */
    public static function missing(string $path): bool;

    /**
     * Get the contents of a file.
     *
     * @param  string  $path
     * @param  bool  $lock
     * @return string
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public static function get(string $path, bool $lock = false): string;

    /**
     * Get contents of a file with shared access.
     *
     * @param  string  $path
     * @return string
     */
    public static function sharedGet(string $path): string;

    /**
     * Get the returned value of a file.
     *
     * @param  string  $path
     * @param  array  $data
     * @return mixed
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public static function getRequire(string $path, array $data = []): mixed;

    /**
     * Require the given file once.
     *
     * @param  string  $path
     * @param  array  $data
     * @return mixed
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public static function requireOnce(string $path, array $data = []): mixed;

    /**
     * Get the contents of a file one line at a time.
     *
     * @param  string  $path
     * @return \Illuminate\Support\LazyCollection
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public static function lines(string $path): \Illuminate\Support\LazyCollection;

    /**
     * Get the hash of the file at the given path.
     *
     * @param  string  $path
     * @param  string  $algorithm
     * @return string
     */
    public static function hash(string $path, string $algorithm = 'md5'): string;

    /**
     * Write the contents of a file.
     *
     * @param  string  $path
     * @param  string  $contents
     * @param  bool  $lock
     * @return int|bool
     */
    public static function put(string $path, string $contents, bool $lock = false): bool|int;

    /**
     * Write the contents of a file, replacing it atomically if it already exists.
     *
     * @param  string  $path
     * @param  string  $content
     * @return void
     */
    public static function replace(string $path, string $content): void;

    /**
     * Replace a given string within a given file.
     *
     * @param  array|string  $search
     * @param  array|string  $replace
     * @param  string  $path
     * @return void
     */
    public static function replaceInFile(array|string $search, array|string $replace, string $path): void;

    /**
     * Prepend to a file.
     *
     * @param  string  $path
     * @param  string  $data
     * @return int
     */
    public static function prepend(string $path, string $data): int;

    /**
     * Append to a file.
     *
     * @param  string  $path
     * @param  string  $data
     * @return int
     */
    public static function append(string $path, string $data): int;

    /**
     * Get or set UNIX mode of a file or directory.
     *
     * @param  string  $path
     * @param  int|null  $mode
     * @return mixed
     */
    public static function chmod(string $path, int $mode = null): mixed;

    /**
     * Delete the file at a given path.
     *
     * @param  string|array  $paths
     * @return bool
     */
    public static function delete(array|string $paths): bool;

    /**
     * Move a file to a new location.
     *
     * @param  string  $path
     * @param  string  $target
     * @return bool
     */
    public static function move(string $path, string $target): bool;

    /**
     * Copy a file to a new location.
     *
     * @param  string  $path
     * @param  string  $target
     * @return bool
     */
    public static function copy(string $path, string $target): bool;

    /**
     * Create a symlink to the target file or directory. On Windows, a hard link is created if the target is a file.
     *
     * @param  string  $target
     * @param  string  $link
     * @return void
     */
    public static function link(string $target, string $link): void;

    /**
     * Create a relative symlink to the target file or directory.
     *
     * @param  string  $target
     * @param  string  $link
     * @return void
     *
     * @throws \RuntimeException
     */
    public static function relativeLink(string $target, string $link): void;

    /**
     * Extract the file name from a file path.
     *
     * @param  string  $path
     * @return string
     */
    public static function name(string $path): string;

    /**
     * Extract the trailing name component from a file path.
     *
     * @param  string  $path
     * @return string
     */
    public static function basename(string $path): string;

    /**
     * Extract the parent directory from a file path.
     *
     * @param  string  $path
     * @return string
     */
    public static function dirname(string $path): string;

    /**
     * Extract the file extension from a file path.
     *
     * @param  string  $path
     * @return string
     */
    public static function extension(string $path): string;

    /**
     * Guess the file extension from the mime-type of a given file.
     *
     * @param  string  $path
     * @return string|null
     *
     * @throws \RuntimeException
     */
    public static function guessExtension(string $path): ?string;

    /**
     * Get the file type of the given file.
     *
     * @param  string  $path
     * @return string
     */
    public static function type(string $path): string;

    /**
     * Get the mime-type of a given file.
     *
     * @param  string  $path
     * @return string|false
     */
    public static function mimeType(string $path): bool|string;

    /**
     * Get the file size of a given file.
     *
     * @param  string  $path
     * @return int
     */
    public static function size(string $path): int;

    /**
     * Get the file's last modification time.
     *
     * @param  string  $path
     * @return int
     */
    public static function lastModified(string $path): int;

    /**
     * Determine if the given path is a directory.
     *
     * @param  string  $directory
     * @return bool
     */
    public static function isDirectory(string $directory): bool;

    /**
     * Determine if the given path is a directory that does not contain any other files or directories.
     *
     * @param  string  $directory
     * @param  bool  $ignoreDotFiles
     * @return bool
     */
    public static function isEmptyDirectory(string $directory, bool $ignoreDotFiles = false): bool;

    /**
     * Determine if the given path is readable.
     *
     * @param  string  $path
     * @return bool
     */
    public static function isReadable(string $path): bool;

    /**
     * Determine if the given path is writable.
     *
     * @param  string  $path
     * @return bool
     */
    public static function isWritable(string $path): bool;

    /**
     * Determine if two files are the same by comparing their hashes.
     *
     * @param  string  $firstFile
     * @param  string  $secondFile
     * @return bool
     */
    public static function hasSameHash(string $firstFile, string $secondFile): bool;

    /**
     * Determine if the given path is a file.
     *
     * @param  string  $file
     * @return bool
     */
    public static function isFile(string $file): bool;

    /**
     * Find path names matching a given pattern.
     *
     * @param  string  $pattern
     * @param  int  $flags
     * @return array
     */
    public static function glob(string $pattern, int $flags = 0): array;

    /**
     * Get an array of all files in a directory.
     *
     * @param  string  $directory
     * @param  bool  $hidden
     * @return \Symfony\Component\Finder\SplFileInfo[]
     */
    public static function files(string $directory, bool $hidden = false): array;

    /**
     * Get all the files from the given directory (recursive).
     *
     * @param  string  $directory
     * @param  bool  $hidden
     * @return \Symfony\Component\Finder\SplFileInfo[]
     */
    public static function allFiles(string $directory, bool $hidden = false): array;

    /**
     * Get all the directories within a given directory.
     *
     * @param  string  $directory
     * @return array
     */
    public static function directories(string $directory): array;

    /**
     * Ensure a directory exists.
     *
     * @param  string  $path
     * @param  int  $mode
     * @param  bool  $recursive
     * @return void
     */
    public static function ensureDirectoryExists(string $path, int $mode = 0755, bool $recursive = true): void;

    /**
     * Create a directory.
     *
     * @param  string  $path
     * @param  int  $mode
     * @param  bool  $recursive
     * @param  bool  $force
     * @return bool
     */
    public static function makeDirectory(string $path, int $mode = 0755, bool $recursive = false, bool $force = false): bool;

    /**
     * Move a directory.
     *
     * @param  string  $from
     * @param  string  $to
     * @param  bool  $overwrite
     * @return bool
     */
    public static function moveDirectory(string $from, string $to, bool $overwrite = false): bool;

    /**
     * Copy a directory from one location to another.
     *
     * @param  string  $directory
     * @param  string  $destination
     * @param  int|null  $options
     * @return bool
     */
    public static function copyDirectory(string $directory, string $destination, int|null $options = null): bool;

    /**
     * Recursively delete a directory.
     *
     * The directory itself may be optionally preserved.
     *
     * @param  string  $directory
     * @param  bool  $preserve
     * @return bool
     */
    public static function deleteDirectory(string $directory, bool $preserve = false): bool;

    /**
     * Remove all the subdirectories within a given directory.
     *
     * @param  string  $directory
     * @return bool
     */
    public static function deleteDirectories(string $directory): bool;

    /**
     * Empty the specified directory of all files and folders.
     *
     * @param  string  $directory
     * @return bool
     */
    public static function cleanDirectory(string $directory): bool;
}
