<?php

namespace Hyde\Framework\Commands;

use Exception;
use Hyde\Framework\Hyde;
use Hyde\Framework\Services\BuildService;
use LaravelZero\Framework\Commands\Command;

/**
 * Hyde Command to build a single static site file.
 */
class HydeRebuildStaticSiteCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'rebuild
        {path : The relative file path (example: _posts/hello-world.md)}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Run the static site builder for a single file';

    /**
     * The Service Class.
     * @var BuildService
     */
    protected BuildService $service;

    /**
     * The source path.
     * @var string
     */
    public string $path;

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $time_start = microtime(true);

        $this->path = $this->sanitizePathString($this->argument('path'));

        try {
            $this->validate();
        } catch (Exception $exception) {
            return $this->handleException($exception);
        }


        $this->service = new BuildService($this->path);

        try {
            $this->service->execute();
        } catch (Exception $exception) {
            return $this->handleException($exception);
        }

        $time_end = microtime(true);
        $execution_time = ($time_end - $time_start);

        $this->info(sprintf(
            "Created %s in %s seconds. (%sms)",
            $this->createClickableFilepath($this->service->builder->createdFilePath),
            number_format(
                $execution_time,
                2
            ),
            number_format(($execution_time * 1000), 2)
        ));

        return 0;
    }

    /**
     * Perform a basic sanitation to strip trailing characters.
     * @param string $path
     * @return string
     */
    public function sanitizePathString(string $path): string
    {
        return ltrim($path, '.\\/');
    }

    /**
     * Validate the path to catch common errors.
     * @throws Exception
     */
    public function validate(): void
    {
        if (!(
            str_starts_with($this->path, '_docs') ||
            str_starts_with($this->path, '_posts') ||
            str_starts_with($this->path, '_pages') ||
            str_starts_with($this->path, 'resources/views/pages')
        )) {
            throw new Exception("Path [$this->path] is not in a valid source directory.", 400);
        }

        if (!file_exists(Hyde::path($this->path))) {
            throw new Exception("File [$this->path] not found.", 404);
        }
    }

    /**
     * Output the contents of an exception.
     * @param Exception $exception
     * @return int Error code
     */
    public function handleException(Exception $exception): int
    {
        $this->error('Something went wrong!');
        $this->warn($exception->getMessage());

        return $exception->getCode();
    }

    /**
     * Create a filepath that can be opened in the browser from a terminal.
     * @param string $filepath
     * @return string
     */
    public function createClickableFilepath(string $filepath)
    {
        return 'file://'.str_replace(
            '\\',
            '/',
            realpath($filepath)
        );
    }
}
