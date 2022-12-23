<?php

declare(strict_types=1);

namespace Hyde\Framework\Actions;

use Hyde\Hyde;
use Hyde\Markdown\Models\MarkdownDocument;
use Spatie\YamlFrontMatter\YamlFrontMatter;

/**
 * Prepares a Markdown file for further usage by extracting the Front Matter and creating MarkdownDocument object.
 *
 * @see \Hyde\Framework\Testing\Feature\MarkdownFileParserTest
 */
class MarkdownFileParser
{
    /**
     * The extracted Front Matter.
     */
    public array $matter = [];

    /**
     * The extracted Markdown body.
     */
    public string $markdown = '';

    public function __construct(string $localFilepath)
    {
        $stream = file_get_contents(Hyde::path($localFilepath));

        // Check if the file has Front Matter.
        if (str_starts_with($stream, '---')) {
            $document = YamlFrontMatter::markdownCompatibleParse($stream);

            if ($document->matter()) {
                $this->matter = $document->matter();
            }

            if ($document->body()) {
                $this->markdown = $document->body();
            }
        } else {
            $this->markdown = $stream;
        }
    }

    /**
     * Get the processed Markdown file as a MarkdownDocument.
     */
    public function get(): MarkdownDocument
    {
        return new MarkdownDocument($this->matter, $this->markdown);
    }

    public static function parse(string $filepath): MarkdownDocument
    {
        return (new self($filepath))->get();
    }
}
