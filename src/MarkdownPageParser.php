<?php

namespace Hyde\Framework;

use Exception;
use Hyde\Framework\Models\MarkdownPage;
use Hyde\Framework\Services\MarkdownFileService;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\NoReturn;
use JetBrains\PhpStorm\Pure;

/**
 * Parses a Markdown file into an object with support for Front Matter.
 *
 * Note that it does not convert it to HTML.
 */
class MarkdownPageParser
{
    /**
     * The page title.
     *
     * @var string
     */
    public string $title;

    /**
     * The extracted page body.
     *
     * @var string
     */
    public string $body;

    /**
     * @param  string  $slug  of the Markdown file (without extension)
     *
     * @throws Exception if the file cannot be found in _pages
     *
     * @example `new MarkdownPageParser('example-page')`
     */
    public function __construct(protected string $slug)
    {
        if (! file_exists(Hyde::path("_pages/$slug.md"))) {
            throw new Exception("File _pages/$slug.md not found.", 404);
        }

        $this->execute();
    }

    /**
     * Handle the parsing job.
     *
     * @return void
     */
    #[NoReturn]
    public function execute(): void
    {
        $document = (new MarkdownFileService(Hyde::path("_pages/$this->slug.md")))->get();

        if (isset($document->matter['title'])) {
            $this->title = $document->matter['title'];
        } else {
            $this->title = $this->findTitleTag($document->body) ??
                Str::title(str_replace('-', ' ', $this->slug));
        }

        $this->body = $document->body;
    }

    /**
     * Attempt to find the title based on the first H1 tag.
     */
    public function findTitleTag(string $stream): string|false
    {
        $lines = explode("\n", $stream);

        foreach ($lines as $line) {
            if (str_starts_with($line, '# ')) {
                return trim(substr($line, 2), ' ');
            }
        }

        return false;
    }

    /**
     * Get the Markdown Page Object.
     *
     * @return MarkdownPage
     */
    #[Pure]
    public function get(): MarkdownPage
    {
        return new MarkdownPage([], $this->body, $this->slug, $this->title);
    }
}
