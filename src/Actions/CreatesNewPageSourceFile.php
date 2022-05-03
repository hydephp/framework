<?php

namespace Hyde\Framework\Actions;

use Exception;
use Hyde\Framework\Hyde;
use Hyde\Framework\Models\BladePage;
use Hyde\Framework\Models\DocumentationPage;
use Hyde\Framework\Models\MarkdownPage;
use Illuminate\Support\Str;

/**
 * Scaffold a new Markdown, Blade, or documentation page.
 *
 * @see \Tests\Feature\Actions\CreatesNewPageSourceFileTest
 */
class CreatesNewPageSourceFile
{
    /**
     * The Page title.
     *
     * @var string
     */
    public string $title;

    /**
     * The Page slug.
     */
    public string $slug;

    /**
     * The file path.
     */
    public string $path;

    /**
     * Construct the class.
     *
     * @param  string  $title  - The page title, will be used to generate the slug
     * @param  string  $type  - The page type, either 'markdown' or 'blade'
     * @param  bool  $force  - Overwrite any existing files
     *
     * @throws Exception if the page type is not 'markdown' or 'blade'
     */
    public function __construct(string $title, string $type = MarkdownPage::class, public bool $force = false)
    {
        $this->title = $title;
        $this->slug = Str::slug($title);

        $this->createPage($type);
    }

    /**
     * Check if the file can be saved.
     *
     * @throws Exception if the file already exists and cannot be overwritten
     */
    public function canSaveFile(string $path): void
    {
        if (file_exists($path) && ! $this->force) {
            throw new Exception("File $path already exists!", 409);
        }
    }

    /**
     * Create the page.
     *
     * @param  string  $type  - The page type, either 'markdown' or 'blade'
     * @return int|false the size of the file created, or false on failure.
     *
     * @throws Exception if the page type is not 'markdown' or 'blade'
     */
    public function createPage(string $type): int|false
    {
        if ($type === MarkdownPage::class) {
            return $this->createMarkdownFile();
        }
        if ($type === BladePage::class) {
            return $this->createBladeFile();
        }

        if ($type === DocumentationPage::class) {
            return $this->createDocumentationFile();
        }

        throw new Exception('The page type must be either "markdown", "blade", or "documentation"');
    }

    /**
     * Create the Markdown file.
     *
     * @return int|false the size of the file created, or false on failure.
     *
     * @throws Exception if the file cannot be saved.
     */
    public function createMarkdownFile(): int|false
    {
        $this->path = Hyde::path("_pages/$this->slug.md");

        $this->canSaveFile($this->path);

        return file_put_contents(
            $this->path,
            "---\ntitle: $this->title\n---\n\n# $this->title\n"
        );
    }

    /**
     * Create the Blade file.
     *
     * @return int|false the size of the file created, or false on failure.
     *
     * @throws Exception if the file cannot be saved.
     */
    public function createBladeFile(): int|false
    {
        $this->path = Hyde::path("_pages/$this->slug.blade.php");

        $this->canSaveFile($this->path);

        return file_put_contents(
            $this->path,
            <<<EOF
@extends('hyde::layouts.app')
@section('content')
@php(\$title = "$this->title")

<main class="mx-auto max-w-7xl py-16 px-8">
	<h1 class="text-center text-3xl font-bold">$this->title</h1>
</main>

@endsection

EOF
        );
    }

    public function createDocumentationFile(): int|false
    {
        $this->path = Hyde::path("_docs/$this->slug.md");

        $this->canSaveFile($this->path);

        return file_put_contents(
            $this->path,
            "# $this->title\n"
        );
    }
}
