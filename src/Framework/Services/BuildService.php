<?php

declare(strict_types=1);

namespace Hyde\Framework\Services;

use Hyde\Facades\Site;
use Hyde\Foundation\Facades\Routes;
use Hyde\Foundation\Kernel\RouteCollection;
use Hyde\Framework\Actions\StaticPageBuilder;
use Hyde\Framework\Concerns\InteractsWithDirectories;
use Hyde\Hyde;
use Hyde\Pages\Concerns\HydePage;
use Hyde\Support\Filesystem\MediaFile;
use Hyde\Support\Models\Route;
use Illuminate\Console\Concerns\InteractsWithIO;
use Illuminate\Console\OutputStyle;
use function collect;

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
        collect($this->getPageTypes())->each(function (string $pageClass): void {
            $this->compilePagesForClass($pageClass);
        });
    }

    public function transferMediaAssets(): void
    {
        $this->needsDirectory(Hyde::siteMediaPath());

        $this->comment('Transferring Media Assets...');
        $this->withProgressBar(MediaFile::files(), function (string $identifier): void {
            $sitePath = Hyde::siteMediaPath($identifier);
            $this->needsParentDirectory($sitePath);
            copy(Hyde::mediaPath($identifier), $sitePath);
        });

        $this->newLine(2);
    }

    /**
     * @param  class-string<\Hyde\Pages\Concerns\HydePage>  $pageClass
     */
    protected function compilePagesForClass(string $pageClass): void
    {
        $this->comment("Creating {$this->getClassPluralName($pageClass)}...");

        $collection = Routes::getRoutes($pageClass);

        $this->withProgressBar($collection, function (Route $route): void {
            (new StaticPageBuilder($route->getPage()))->__invoke();
        });

        $this->newLine(2);
    }

    protected function getClassPluralName(string $pageClass): string
    {
        return preg_replace('/([a-z])([A-Z])/', '$1 $2', class_basename($pageClass)).'s';
    }

    /** @return array<class-string<\Hyde\Pages\Concerns\HydePage>> */
    protected function getPageTypes(): array
    {
        return Hyde::pages()->map(function (HydePage $page): string {
            return $page::class;
        })->unique()->values()->toArray();
    }
}
