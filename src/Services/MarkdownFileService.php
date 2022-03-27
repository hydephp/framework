<?php

namespace Hyde\Framework\Services;

use Exception;
use Hyde\Framework\Hyde;
use JetBrains\PhpStorm\Pure;
use Spatie\YamlFrontMatter\YamlFrontMatter;
use Hyde\Framework\Models\MarkdownDocument;

/**
 * Prepares a Markdown file for further usage.
 *
 * Main functionality is as a pre-processor to split out Front Matter from the Body.
 *
 * Suggested usage is to supply a relative file slug path, and then retrieve a MarkdownDocument object.
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
    public function getDocument(): MarkdownDocument
    {
        return new MarkdownDocument($this->matter, $this->body);
    }
}