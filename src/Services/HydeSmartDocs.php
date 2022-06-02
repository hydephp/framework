<?php

namespace Hyde\Framework\Services;

use Hyde\Framework\Concerns\FacadeHelpers\HydeSmartDocsFacade;
use Hyde\Framework\Models\DocumentationPage;

/**
 * Class to make Hyde documentation pages smarter,
 * allowing for rich and dynamic content.
 */
class HydeSmartDocs
{
    use HydeSmartDocsFacade;

    protected DocumentationPage $page;
    protected string $html;

    protected string $header;
    protected string $body;
    protected string $footer;

    public function __construct(DocumentationPage $page, string $html)
    {
        $this->page = $page;
        $this->html = $html;
    }

    public function renderHeader(): string
    {
        return $this->header;
    }

    public function renderBody(): string
    {
        return $this->body;
    }

    public function renderFooter(): string
    {
        return $this->footer;
    }

    /** @internal */
    public function process(): self
    {
        //

        return $this;
    }
}