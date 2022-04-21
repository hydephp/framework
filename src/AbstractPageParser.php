<?php

namespace Hyde\Framework;

use Hyde\Framework\Actions\ValidatesExistence;
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
abstract class AbstractPageParser implements PageParserContract
{
    use ValidatesExistence;

    /**
     * @var string of the page to parse.
     */
    protected string $slug;

    /**
     * @var string the parser is for.
     */
    protected string $pageModel = AbstractPage::class;

    /**
     * Construct the class.
     *
     * @throws \Exception if the source file does not exist.
     */
    public function __construct(string $slug)
    {
        $this->slug = $slug;
        $this->validateExistence($this->pageModel, $slug);
        $this->execute();
    }
}
