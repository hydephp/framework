<?php

namespace Hyde\Framework\Services;

use Exception;
use Hyde\Framework\DocumentationPageParser;
use Hyde\Framework\MarkdownPageParser;
use Hyde\Framework\MarkdownPostParser;
use Hyde\Framework\Models\BladePage;
use Hyde\Framework\Models\DocumentationPage;
use Hyde\Framework\Models\MarkdownPage;
use Hyde\Framework\Models\MarkdownPost;
use Hyde\Framework\StaticPageBuilder;

/**
 * Build static pages, but intelligently.
 *
 * Runs the static page builder for a given path.
 */
class BuildService
{
    /**
     * The source file to build.
     * Should be relative to the Hyde::path() helper.
     *
     * @var string
     */
    public string $filepath;

    /**
     * The model of the source file.
     *
     * @var string
     *
     * @internal
     */
    public string $model;

    /**
     * The page builder instance.
     * Used to get debug output from the builder.
     *
     * @var StaticPageBuilder
     */
    public StaticPageBuilder $builder;

    /**
     * @param  string  $filepath
     */
    public function __construct(string $filepath)
    {
        $this->filepath = $filepath;
    }

    /**
     * Execute the service action.
     *
     * @throws Exception
     */
    public function execute()
    {
        $this->model = $this->determineModel();

        $this->handle();
    }

    /**
     * Handle the service action.
     *
     * @throws Exception
     */
    public function handle(): StaticPageBuilder
    {
        if ($this->model === MarkdownPost::class) {
            $slug = basename(str_replace('_posts/', '', $this->filepath), '.md');

            return $this->builder = (new StaticPageBuilder((new MarkdownPostParser($slug))->get(), true));
        }

        if ($this->model === MarkdownPage::class) {
            $slug = basename(str_replace('_pages/', '', $this->filepath), '.md');

            return $this->builder = (new StaticPageBuilder((new MarkdownPageParser($slug))->get(), true));
        }

        if ($this->model === DocumentationPage::class) {
            $slug = basename(str_replace('_docs/', '', $this->filepath), '.md');

            return $this->builder = (new StaticPageBuilder((new DocumentationPageParser($slug))->get(), true));
        }

        if ($this->model === BladePage::class) {
            $slug = basename(str_replace('resources/views/pages/', '', $this->filepath), '.blade.php');

            return $this->builder = (new StaticPageBuilder((new BladePage($slug)), true));
        }

        throw new Exception('Could not run the builder.', 400);
    }

    /**
     * Determine the proper model of the source file.
     *
     * @throws Exception
     */
    public function determineModel(): string
    {
        if (str_starts_with($this->filepath, '_posts')) {
            return $this->model = MarkdownPost::class;
        } elseif (str_starts_with($this->filepath, '_pages')) {
            return $this->model = MarkdownPage::class;
        } elseif (str_starts_with($this->filepath, '_docs')) {
            return $this->model = DocumentationPage::class;
        } elseif (str_starts_with($this->filepath, 'resources/views/pages')) {
            return $this->model = BladePage::class;
        } else {
            throw new Exception('Invalid source path.', 400);
        }
    }


    /**
     * Create a filepath that can be opened in the browser from a terminal.
     *
     * @param  string  $filepath
     * @return string
     */
    public static function createClickableFilepath(string $filepath): string
    {
        return 'file://'.str_replace(
            '\\',
            '/',
            realpath($filepath)
        );
    }
}
