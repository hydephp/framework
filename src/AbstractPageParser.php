<?php

namespace Hyde\Framework;

use Hyde\Framework\Models\AbstractPage;

/**
 * Abstract base class for all page parsers.
 *
 * Page Parsers are responsible for parsing a source file into a Page object,
 * and may also conduct pre-processing and/or data validation/assembly.
 *
 * Note that the Page Parsers do not compile any HTML or Markdown.
 *
 * To ensure that all page parsing jobs are handled consistently,
 * all page parsers should extend this class.
 */
abstract class AbstractPageParser
{
    /**
     * Construct the class.
     * @param string $slug of the page to parse.
     */
    abstract public function __construct(string $slug);

    /**
     * Handle the parsing job.
     * @return void
     */
    abstract public function execute(): void;

    /**
     * Get the parsed page object.
     * @return AbstractPage
     */
    abstract public function get(): AbstractPage;
}