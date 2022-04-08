<?php

namespace Hyde\Framework;

use Exception;
use Hyde\Framework\Models\DocumentationPage;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\NoReturn;
use JetBrains\PhpStorm\Pure;

class DocumentationPageParser extends AbstractPageParser
{
    private string $filepath;

    public string $body;

    public string $title;

    public function __construct(protected string $slug)
    {
        $this->filepath = Hyde::path("_docs/$slug.md");
        if (!file_exists($this->filepath)) {
            throw new Exception("File _docs/$slug.md not found.", 404);
        }

        $this->execute();
    }

    public function execute(): void
    {
        $stream = file_get_contents($this->filepath);

        $this->title = $this->findTitleTag($stream) ??
            Str::title(str_replace('-', ' ', $this->slug));

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

    public function get(): DocumentationPage
    {
        return new DocumentationPage(
            matter: [],
            body: $this->body,
            title: $this->title,
            slug: $this->slug
        );
    }
}
