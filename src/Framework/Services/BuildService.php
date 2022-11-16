<?php

declare(strict_types=1);

namespace Hyde\Framework\Services;

use Hyde\Facades\Site;
use Hyde\Foundation\RouteCollection;
use Hyde\Framework\Actions\StaticPageBuilder;
use Hyde\Framework\Concerns\InteractsWithDirectories;
use Hyde\Hyde;
use Hyde\Support\Models\Route;
use Illuminate\Console\Concerns\InteractsWithIO;
use Illuminate\Console\OutputStyle;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

/**
 * Moves logic from the build command to a service.
 *
 * Handles the build loop which generates the static site.
 *
 * @see \Hyde\Console\Commands\BuildSiteCommand
 * @see \Hyde\Framework\Testing\Feature\StaticSiteServiceTest
 */
class BuildService
{
    use InteractsWithIO;
    use InteractsWithDirectories;

    protected RouteCollection $router;

    public function __construct(OutputStyle $output)
    {
        $this->output = $output;

        $this->router = Hyde::routes();
    }

    public function compileStaticPages(): void
    {
        $this->getClassNamesForDiscoveredPageModels()->each(function (string $pageClass) {
            $this->compilePagesForClass($pageClass);
        });
    }

    public function cleanOutputDirectory(): void
    {
        if (config('hyde.empty_output_directory', true)) {
            $this->warn('Removing all files from build directory.');

            if ($this->isItSafeToCleanOutputDirectory()) {
                array_map('unlink', glob(Hyde::sitePath('*.{html,json}'), GLOB_BRACE));
                File::cleanDirectory(Hyde::sitePath('media'));
            }
        }
    }

    public function transferMediaAssets(): void
    {
        $this->needsDirectory(Hyde::sitePath('media'));

        $this->comment('Transferring Media Assets...');

        $this->withProgressBar(DiscoveryService::getMediaAssetFiles(), function (string $filepath): void {
            copy($filepath, Hyde::sitePath('media/'.basename($filepath)));
        });

        $this->newLine(2);
    }

    /**
     * @return \Illuminate\Support\Collection<array-key, class-string<\Hyde\Pages\Concerns\HydePage>>
     */
    protected function getClassNamesForDiscoveredPageModels(): Collection
    {
        return $this->router->getRoutes()->map(function (Route $route): string {
            return $route->getPageClass();
        })->unique();
    }

    /**
     * @param  class-string<\Hyde\Pages\Concerns\HydePage>  $pageClass
     */
    protected function compilePagesForClass(string $pageClass): void
    {
        $this->comment("Creating {$this->getClassPluralName($pageClass)}...");

        $collection = $this->router->getRoutes($pageClass);

        $this->withProgressBar($collection, function (Route $route): void {
            (new StaticPageBuilder($route->getPage()))->__invoke();
        });

        $this->newLine(2);
    }

    protected function getClassPluralName(string $pageClass): string
    {
        return preg_replace('/([a-z])([A-Z])/', '$1 $2', class_basename($pageClass)).'s';
    }

    protected function isItSafeToCleanOutputDirectory(): bool
    {
        if (! $this->isOutputDirectoryWhitelisted() && ! $this->askIfUnsafeDirectoryShouldBeEmptied()) {
            $this->info('Output directory will not be emptied.');

            return false;
        }

        return true;
    }

    protected function isOutputDirectoryWhitelisted(): bool
    {
        return in_array(basename(Hyde::sitePath()), $this->safeOutputDirectories());
    }

    protected function askIfUnsafeDirectoryShouldBeEmptied(): bool
    {
        return $this->confirm(sprintf(
            'The configured output directory (%s) is potentially unsafe to empty. '.
            'Are you sure you want to continue?',
            Site::$outputPath
        ));
    }

    protected function safeOutputDirectories(): array
    {
        return config('hyde.safe_output_directories', ['_site', 'docs', 'build']);
    }
}
