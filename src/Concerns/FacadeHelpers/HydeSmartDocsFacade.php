<?php

namespace Hyde\Framework\Concerns\FacadeHelpers;

use Hyde\Framework\Helpers\Features;
use Hyde\Framework\Models\DocumentationPage;

/**
 * Provide static facade methods, and instance helpers for HydeSmartDocs.
 * @see \Hyde\Framework\Services\HydeSmartDocs
 */
trait HydeSmartDocsFacade
{
    public static function create(DocumentationPage $page, string $html): static
    {
        return (new static($page, $html))->process();
    }

    public static function enabled(): bool
    {
        return config('docs.smart_docs', true);
    }

    public function hasTorchlight(): bool
    {
        return Features::hasTorchlight() && str_contains($this->html, 'Syntax highlighted by torchlight.dev');
    }
}