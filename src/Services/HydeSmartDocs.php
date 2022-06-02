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

    public function __construct(DocumentationPage $page, string $html)
    {
        $this->page = $page;
        $this->html = $html;
    }
}