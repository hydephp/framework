<?php

namespace Hyde\Framework\Contracts;

use Hyde\Framework\Concerns\HasPageMetadata;

/**
 * To ensure compatability with the Hyde Framework,
 * all Page Models must extend this class.
 *
 * Markdown-based Pages should extend MarkdownDocument.
 */
abstract class AbstractPage implements PageContract
{
    use HasPageMetadata;

    public static string $sourceDirectory;
    public static string $fileExtension;
    public static string $parserClass;

    public string $slug;

    public function getCurrentPagePath(): string
    {
        return $this->slug;
    }
}
