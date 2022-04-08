<?php

namespace Hyde\Framework;

use Exception;
use Hyde\Framework\Models\MarkdownPost;
use Hyde\Framework\Services\MarkdownFileService;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\NoReturn;
use JetBrains\PhpStorm\Pure;

/**
 * Parses a Markdown file into an object with support for Front Matter.
 *
 * Note that it does not convert it to HTML.
 */
class MarkdownPostParser
{
    /**
     * The extracted Front Matter.
     *
     * @var array
     */
    public array $matter;

    /**
     * The extracted Markdown body.
     *
     * @var string
     */
    public string $body;

    /**
     * The page title.
     *
     * @var string
     */
    public string $title;

    /**
     * @param  string  $slug  of the Markdown file (without extension)
     *
     * @throws Exception if the file cannot be found in _posts
     *
     * @example `new MarkdownPostParser('example-post')`
     */
    public function __construct(protected string $slug)
    {
        if (! file_exists(Hyde::path("_posts/$slug.md"))) {
            throw new Exception("File _posts/$slug.md not found.", 404);
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
        $document = (new MarkdownFileService(Hyde::path("_posts/$this->slug.md")))->get();

        $this->matter = array_merge($document->matter, [
            'slug' => $this->slug, // Makes sure the slug cannot be set from the front matter
        ]);

        if (isset($document->matter['title'])) {
            $this->title = $document->matter['title'];
        } else {
            $this->title = Str::title(str_replace('-', ' ', $this->slug));
        }

        $this->body = $document->body;
    }

    /**
     * Get the Markdown Post Object.
     *
     * @return MarkdownPost
     */
    public function get(): MarkdownPost
    {
        return new MarkdownPost(matter: $this->matter, body: $this->body, title: $this->title, slug: $this->slug);
    }
}
