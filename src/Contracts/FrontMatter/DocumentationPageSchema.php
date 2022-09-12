<?php

namespace Hyde\Framework\Contracts\FrontMatter;

/**
 * @see \Hyde\Framework\Models\Pages\DocumentationPage
 */
interface DocumentationPageSchema
{
    public const DOCUMENTATION_PAGE_SCHEMA = [
        'category'  => 'string',
        'navigation'    => 'array|navigation',
    ];
}
