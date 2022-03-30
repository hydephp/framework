<?php

namespace Hyde\Framework;

use Hyde\Framework\Actions\MarkdownConverter;
use Hyde\Framework\Models\BladePage;
use Hyde\Framework\Models\DocumentationPage;
use Hyde\Framework\Models\MarkdownPage;
use Hyde\Framework\Models\MarkdownPost;
use JetBrains\PhpStorm\ArrayShape;

/**
 * Converts a Page Model into a static HTML page.
 */
class StaticPageBuilder
{
    /**
     * Debug output: The size of the created file.
     *
     * @var int|bool|void|null
     */
    public null|int|false $createdFileSize;
    /**
     * Debug output: The path of the created file.
     *
     * @var string|null
     */
    public null|string $createdFilePath;

    /**
     * Construct the class.
     *
     * @param  MarkdownPost|MarkdownPage|BladePage|DocumentationPage  $page  the Page to compile into HTML
     * @param  bool  $runAutomatically  if set to true the class will invoke when constructed
     */
    public function __construct(
        protected MarkdownPost|MarkdownPage|BladePage|DocumentationPage $page,
        bool $runAutomatically = false
    ) {
        if ($runAutomatically) {
            $this->createdFileSize = $this->__invoke();
        }
    }

    /**
     * Run the page builder.
     *
     * @return bool|int|void
     */
    public function __invoke()
    {
        if ($this->page instanceof MarkdownPost) {
            return $this->save('posts/'.$this->page->slug, $this->compilePost());
        }

        if ($this->page instanceof MarkdownPage) {
            return $this->save($this->page->slug, $this->compilePage());
        }

        if ($this->page instanceof BladePage) {
            return $this->save($this->page->view, $this->compileView());
        }

        if ($this->page instanceof DocumentationPage) {
            if (! file_exists(Hyde::path('_site/'.Hyde::docsDirectory()))) {
                mkdir(Hyde::path('_site/'.Hyde::docsDirectory()));
            }

            return $this->save(Hyde::docsDirectory().'/'.$this->page->slug, $this->compileDocs());
        }
    }

    /**
     * Get the debug data.
     *
     * @param  bool  $relativeFilePath  should the returned filepath be relative instead of absolute?
     * @return array
     */
    #[ArrayShape(['createdFileSize' => 'mixed', 'createdFilePath' => 'mixed'])]
    public function getDebugOutput(bool $relativeFilePath = true): array
    {
        return [
            'createdFileSize' => $this->createdFileSize,
            'createdFilePath' => $relativeFilePath
                ? str_replace(Hyde::path(), '', $this->createdFilePath)
                : $this->createdFilePath,
        ];
    }

    /**
     * Save the compiled HTML to file.
     *
     * @param  string  $location  of the output file relative to _site/
     * @param  string  $contents  to save to the file
     */
    private function save(string $location, string $contents): bool|int
    {
        $path = Hyde::path("_site/$location.html");
        $this->createdFilePath = $path;

        return file_put_contents($path, $contents);
    }

    /**
     * Compile a Post into HTML using the Blade View.
     *
     * @return string
     */
    private function compilePost(): string
    {
        return view('hyde::layouts/post')->with([
            'post' => $this->page,
            'title' => $this->page->matter['title'],
            'markdown' => MarkdownConverter::parse($this->page->body),
            'currentPage' => 'posts/'.$this->page->slug,
        ])->render();
    }

    /**
     * Compile a Documentation page into HTML using the Blade View.
     *
     * @return string
     */
    private function compileDocs(): string
    {
        return view('hyde::layouts/docs')->with([
            'docs' => $this->page,
            'title' => $this->page->title,
            'markdown' => MarkdownConverter::parse($this->page->content),
            'currentPage' => trim(config('hyde.docsDirectory', 'docs'), '\\/').'/'.$this->page->slug,
        ])->render();
    }

    /**
     * Compile a Markdown Page into HTML using the Blade View.
     *
     * @return string
     */
    private function compilePage(): string
    {
        return view('hyde::layouts/page')->with([
            'title' => $this->page->title,
            'markdown' => MarkdownConverter::parse($this->page->body),
            'currentPage' => $this->page->slug,
        ])->render();
    }

    /**
     * Compile a custom Blade View into HTML.
     *
     * @return string
     */
    private function compileView(): string
    {
        return view('pages/'.$this->page->view, [
            'currentPage' => $this->page->view,
        ])->render();
    }
}
