<?php

namespace Hyde\Framework;

use Exception;
use Hyde\Framework\Models\DocumentationPage;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\NoReturn;
use JetBrains\PhpStorm\Pure;

/**
 * Parses a Documentation file into an object.
 *
 * Note that it does not convert it to HTML.
 */
class DocumentationPageParser
{
    /** @var string the full path to the Documentation file */
    private string $filepath;

    public string $body;

    public string $title;

    /**
     * @param  string  $slug  of the Documentation file (without extension)
     *
     * @throws Exception if the file cannot be found in _docs
     *
     * @example `new DocumentationPageParser('example-doc')`
     */
    public function __construct(protected string $slug)
    {
        $this->filepath = Hyde::path("_docs/$slug.md");
        if (! file_exists($this->filepath)) {
            throw new Exception("File _docs/$slug.md not found.", 404);
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
        // Get the text stream from the markdown file
        $stream = file_get_contents($this->filepath);

        $this->title = $this->findTitleTag($stream) ?? Str::title(str_replace('-', ' ', $this->slug));

        $this->body = $stream;
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
     * Get the Documentation Page Object.
     *
     * @return DocumentationPage
     */
    #[Pure]
    public function get(): DocumentationPage
    {
        return new DocumentationPage(matter: [], body: $this->body, title: $this->title, slug: $this->slug);
    }
}
