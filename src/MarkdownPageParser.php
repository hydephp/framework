<?php

namespace Hyde\Framework;

use Hyde\Framework\Models\MarkdownPage;
use Hyde\Framework\Services\MarkdownFileService;
use Illuminate\Support\Str;

/**
 * @todo Re-add support for YAML Front Matter.
 */
class MarkdownPageParser extends AbstractPageParser
{
    protected string $pageModel = MarkdownPage::class;
    protected string $slug;

    public string $title;
    public string $body;

    public function execute(): void
    {
        $document = (new MarkdownFileService(
            Hyde::path("_pages/$this->slug.md")
        ))->get();

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

    public function get(): MarkdownPage
    {
        return new MarkdownPage(
            matter: [],
            body: $this->body,
            title: $this->title,
            slug: $this->slug
        );
    }
}
