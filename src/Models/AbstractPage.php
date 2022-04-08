<?php

namespace Hyde\Framework\Models;

/**
 * To ensure compatability with the Hyde Framework,
 * all Page Models must extend this class.
 * 
 * Markdown-based Pages should extend MarkdownDocument.
 */
abstract class AbstractPage
{
    public static string $sourceDirectory;
    public static string $fileExtension;
    public static string $parserClass;
}
