<?php

namespace Hyde\Framework\Commands;

use Exception;
use Hyde\Framework\Concerns\Internal\TransfersMediaAssetsForBuildCommands;
use Hyde\Framework\Hyde;
use Hyde\Framework\Services\BuildService;
use Hyde\Framework\Services\RebuildService;
use LaravelZero\Framework\Commands\Command;

/**
 * Hyde Command to build a single static site file.
 */
class HydeRebuildStaticSiteCommand extends Command
{
    use TransfersMediaAssetsForBuildCommands;

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
     * The source path.
     *
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

        if ($this->argument('path') === '_media') {
            $this->transferMediaAssets();

            return 0;
        }

        $this->path = $this->sanitizePathString($this->argument('path'));

        try {
            $this->validate();
        } catch (Exception $exception) {
            return $this->handleException($exception);
        }

        (new RebuildService($this->path))->execute();

        $time_end = microtime(true);
        $execution_time = ($time_end - $time_start);

        $this->info(sprintf(
            'Created %s in %s seconds. (%sms)',
            BuildService::createClickableFilepath($this->getOutputPath($this->path)),
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
     *
     * @param  string  $path
     * @return string
     */
    public function sanitizePathString(string $path): string
    {
        return str_replace('\\', '/', trim($path, '.\\/'));
    }

    /**
     * Validate the path to catch common errors.
     *
     * @throws Exception
     */
    public function validate(): void
    {
        if (! (str_starts_with($this->path, '_docs') ||
            str_starts_with($this->path, '_posts') ||
            str_starts_with($this->path, '_pages') ||
            str_starts_with($this->path, '_pages')
        )) {
            throw new Exception("Path [$this->path] is not in a valid source directory.", 400);
        }

        if (! file_exists(Hyde::path($this->path))) {
            throw new Exception("File [$this->path] not found.", 404);
        }
    }

    /**
     * Output the contents of an exception.
     *
     * @param  Exception  $exception
     * @return int Error code
     */
    public function handleException(Exception $exception): int
    {
        $this->error('Something went wrong!');
        $this->warn($exception->getMessage());

        return $exception->getCode();
    }

    /**
     * Get the output path for the given path.
     *
     * @param  string  $path
     * @return string
     */
    public function getOutputPath(string $path): string
    {
        $path = str_replace(Hyde::path(), '', $path);

        if (str_starts_with($path, '_posts')) {
            return Hyde::path(str_replace('_posts', '_site/posts', rtrim($path, '.md').'.html'));
        }

        if (str_starts_with($path, '_docs')) {
            return Hyde::path(str_replace('_docs', '_site/docs', rtrim($path, '.md').'.html'));
        }

        if (str_starts_with($path, '_pages')) {
            $path = str_replace('.blade.php', '.md', $path);

            return Hyde::path(str_replace('_pages', '_site/', rtrim($path, '.md').'.html'));
        }

        return $path;
    }
}
