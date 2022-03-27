<?php

namespace Hyde\Framework\Services;

use JetBrains\PhpStorm\Pure;
use Spatie\YamlFrontMatter\YamlFrontMatter;
use Hyde\Framework\Models\MarkdownDocument;

/**
 * Prepares a Markdown file for further usage.
 * The service splits the Front Matter and creates a Markdown Document Object
 */
class MarkdownFileService
{
    /**
     * The extracted Front Matter
     * @var array
     */
    public array $matter = [];

    /**
     * The extracted Markdown body
     * @var string
     */
    public string $body = "";

    public function __construct(string $filepath)
    {
        $object = YamlFrontMatter::parse(file_get_contents($filepath));

        if ($object->matter()) {
            $this->matter = $object->matter();
        }

        if ($object->body()) {
            $this->body = $object->body();
        }
    }

    /**
     * Get the processed Markdown file as a MarkdownDocument.
     * @return MarkdownDocument
     */
    #[Pure]
    public function get(): MarkdownDocument
    {
        return new MarkdownDocument($this->matter, $this->body);
    }
}