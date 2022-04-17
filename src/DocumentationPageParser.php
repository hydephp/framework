<?php

namespace Hyde\Framework;

use Hyde\Framework\Models\DocumentationPage;
use Hyde\Framework\Services\MarkdownFileService;

class DocumentationPageParser extends AbstractPageParser
{
    protected string $pageModel = DocumentationPage::class;
    protected string $slug;

    public string $title;
    public string $body;

    public function execute(): void
    {
        $document = (new MarkdownFileService(
            Hyde::path("_docs/$this->slug.md")
        ))->get();

        if (isset($document->matter['title'])) {
            $this->title = $document->matter['title'];
        } else {
            $this->title = $this->findTitleTag($document->body) ??
                Hyde::titleFromSlug($this->slug);
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
