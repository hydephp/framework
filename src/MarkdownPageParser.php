<?php

namespace Hyde\Framework;

use Hyde\Framework\Models\MarkdownPage;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\NoReturn;
use JetBrains\PhpStorm\Pure;
use Exception;

/**
 * Parses a Markdown file into an object with support for Front Matter.
 *
 * Note that it does not convert it to HTML.
 */
class MarkdownPageParser
{
    /**
     * @var string the full path to the Markdown file
     */
    private string $filepath;

    /**
     * The extracted page body
     * @var string
     */
    public string $body;

    /**
     * The page title
     * @var string
     */
    public string $title;

    /**
     * @param string $slug of the Markdown file (without extension)
     * @throws Exception if the file cannot be found in _pages
     * @example `new MarkdownPageParser('example-page')`
     */
    public function __construct(protected string $slug)
    {
        $this->filepath = Hyde::path("_pages/$slug.md");
        if (!file_exists($this->filepath)) {
            throw new Exception("File _pages/$slug.md not found.", 404);
        }

        $this->execute();
    }

    /**
     * Handle the parsing job.
     * @return void
     */
    #[NoReturn]
    public function execute(): void
    {
        // Get the text stream from the markdown file
        $stream = file_get_contents($this->filepath);

        $this->title = $this->findTitleTag($stream) ?? Str::title(str_replace('-', ' ', $this->slug));

        $this->body = $stream;
    }

    /**
     * Attempt to find the title based on the first H1 tag
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
     * @return MarkdownPage
     */
    #[Pure]
    public function get(): MarkdownPage
    {
        return new MarkdownPage([], $this->body, $this->slug, $this->title);
    }
}
