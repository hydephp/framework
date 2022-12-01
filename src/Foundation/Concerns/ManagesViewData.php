<?php

declare(strict_types=1);

namespace Hyde\Foundation\Concerns;

use Hyde\Pages\Concerns\HydePage;
use Hyde\Support\Models\Route;
use Illuminate\Support\Facades\View;

/**
 * @internal Single-use trait for the HydeKernel class.
 *
 * @todo Consider if this logic is better suited for a "Render" class solely for handling data related to the current render.
 *       This could then also have a proper schema for the defined data so it can be type-hinted.
 *
 * @see \Hyde\Foundation\HydeKernel
 */
trait ManagesViewData
{
    /**
     * Share data for the page being rendered.
     */
    public function shareViewData(HydePage $page): void
    {
        View::share('page', $page);
        View::share('currentPage', $page->getRouteKey());
        View::share('currentRoute', $page->getRoute());
    }

    /**
     * Get the route key for the page being rendered.
     */
    public function currentPage(): ?string
    {
        return View::shared('currentPage');
    }

    /**
     * Get the route for the page being rendered.
     */
    public function currentRoute(): ?Route
    {
        return View::shared('currentRoute');
    }
}
