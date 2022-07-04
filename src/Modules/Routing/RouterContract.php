<?php

namespace Hyde\Framework\Modules\Routing;

use Illuminate\Support\Collection;

interface RouterContract
{
    /**
     * Construct a new Router instance and discover all routes.
     */
    public function __construct();

    /**
     * Get the Singleton instance of the Router.
     *
     * @return \Hyde\Framework\Modules\Routing\RouterContract
     */
    public static function getInstance(): RouterContract;

    /**
     * Get the routes discovered by the router.
     *
     * @return \Illuminate\Support\Collection<\Hyde\Framework\Modules\Routing\RouteContract>
     */
    public function getRoutes(): Collection;
}
