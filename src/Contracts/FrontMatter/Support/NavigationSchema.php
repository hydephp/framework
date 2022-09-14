<?php

namespace Hyde\Framework\Contracts\FrontMatter\Support;

/**
 * @see \Hyde\Framework\Models\Navigation\NavigationData
 * @see \Hyde\Framework\Concerns\HydePage
 */
interface NavigationSchema
{
    public const NAVIGATION_SCHEMA = [
        'label'     => 'string',
        'group'     => 'string',
        'hidden'    => 'bool',
        'priority'  => 'int',
    ];
}
