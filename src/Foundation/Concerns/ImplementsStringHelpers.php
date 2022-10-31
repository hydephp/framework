<?php

declare(strict_types=1);

namespace Hyde\Foundation\Concerns;

use Illuminate\Support\Str;

/**
 * @internal Single-use trait for the HydeKernel class.
 *
 * @see \Hyde\Foundation\HydeKernel
 */
trait ImplementsStringHelpers
{
    public function makeTitle(string $slug): string
    {
        $alwaysLowercase = ['a', 'an', 'the', 'in', 'on', 'by', 'with', 'of', 'and', 'or', 'but'];

        return ucfirst(str_ireplace(
            $alwaysLowercase,
            $alwaysLowercase,
            Str::headline($slug)
        ));
    }
}
