<?php

namespace Hyde\Framework;

use Hyde\Framework\Models\DocumentationPage;

class DocumentationPageParser extends AbstractPageParser
{
    protected string $pageModel = DocumentationPage::class;
    protected string $slug;

    public string $body;
    public string $title;

    public function execute(): void
    {
        $stream = file_get_contents(Hyde::path("_docs/$this->slug.md"));

        $this->title = $this->findTitleTag($stream) ??
            Hyde::titleFromSlug($this->slug);

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
