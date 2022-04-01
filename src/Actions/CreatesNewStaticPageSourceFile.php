<?php

namespace Hyde\Framework\Actions;

use Hyde\Framework\Hyde;
use Hyde\Framework\Models\BladePage;
use Hyde\Framework\Models\MarkdownPage;
use Illuminate\Support\Str;

/**
 * Scaffold a new Markdown or Blade page
 */
class CreatesNewStaticPageSourceFile
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
	 * Construct the class.
	 *
	 * @param string $title - The page title, will be used to generate the slug
	 * @param string $type - The page type, either 'markdown' or 'blade'
	 */
	public function __construct(string $title, string $type = MarkdownPage::class)
	{
		$this->title = $title;
		$this->slug = Str::slug($title);

		$this->createPage($type);
	}

    /**
     * Create the page.
     *
     * @param string $type - The page type, either 'markdown' or 'blade'
     * @throws \Exception if the page type is not 'markdown' or 'blade'
	 */
	public function createPage(string $type)
	{
		// Check that the page type is either 'markdown' or 'blade'
		if ($type === MarkdownPage::class) {
			return $this->createMarkdownFile();
		} 
		if ($type === BladePage::class) {
			return $this->createBladeFile();
		} 

		throw new \Exception('The page type must be either "markdown" or "blade"');
	}

	/**
	 * Create the Markdown file.
	 */
	public function createMarkdownFile()
	{
		return file_put_contents(
			Hyde::path("_pages/{$this->slug}.md"),
			"---\ntitle: {$this->title}\n---\n\n# {$this->title}\n"
		);
	}

	/**
	 * Create the Blade file.
	 */
	public function createBladeFile()
	{
		return file_put_contents(
			Hyde::path("resources/views/pages/{$this->slug}.blade.php"),
			"@extends('hyde::layouts.app')
@section('content')
@php(\$title = \"{$this->title}\")

<main class=\"mx-auto max-w-7xl py-16 px-8\">
	<h1 class=\"text-center text-3xl font-bold\">{$this->title}</h1>
</main>

@endsection
");
	}
}