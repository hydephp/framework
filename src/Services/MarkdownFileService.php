<?php

namespace Hyde\Framework\Services;

use Exception;
use Hyde\Framework\Hyde;
use Hyde\Framework\Models\MarkdownDocument;
use JetBrains\PhpStorm\Pure;

/**
 * Prepares a Markdown file for further usage.
 *
 * Main functionality is as a pre-processor to split out Front Matter from the Body.
 *
 * Suggested usage is to supply a relative file slug path, and then retrieve a MarkdownDocument object.
 */
class MarkdownFileService
{
    protected string $filepath;

    protected array $lines;

    public array $matter = [];

    public string $body = "";


    /**
     * @example new MarkdownFileService('_posts/hello-world')
     * @param string $relativeSlugPath the slug of the file to process
     * @throws Exception
     */
    public function __construct(string $relativeSlugPath)
    {
        $this->filepath = $this->getFilepath($relativeSlugPath);
        $this->lines = $this->parseFileStream();
    }

    /**
     * Get the qualified filepath or throw exception if file does not exist
     * @param string $relativeSlugPath
     * @return string
     * @throws Exception
     */
    public function getFilepath(string $relativeSlugPath): string
    {
        $filepath = Hyde::path("$relativeSlugPath.md");
        if (!file_exists($filepath)) {
            throw new Exception("File $relativeSlugPath.md not found.", 404);
        }
        return $filepath;
    }

    /**
     * Does the file contain front matter?
     * @return bool
     */
    public function containsFrontMatter(): bool
    {
        return false;
    }

    /**
     * Parse the file contents into an array of the lines.
     * @return array
     */
    public function parseFileStream(): array
    {
        $stream = file_get_contents($this->filepath);
        return explode("\n", $stream);
    }

    /**
     * Split the Front Matter from the Markdown.
     */
    public function split(): void
    {
        $this->body = "";
        $this->matter = $this->parseFrontMatter([]);
    }

    /**
     * Parse array of lines of Front Matter YAML into an associative array.
     * @param array $frontMatterLines
     * @return array
     */
    public function parseFrontMatter(array $frontMatterLines): array
    {
        return [];
    }

    /**
     * Get the processed Markdown file as a MarkdownDocument.
     * @return MarkdownDocument
     */
    #[Pure]
    public function getDocument(): MarkdownDocument
    {
        return new MarkdownDocument($this->matter, $this->body);
    }
}