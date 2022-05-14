<?php

namespace Hyde\Framework\Core;

/**
 * The Hyde Manager is the glue that holds Hyde services together.
 * It's intended to act as an easy way to swap out implementations of
 * various services to extend or modify the default Hyde behavior.
 *
 * The interface implementation should be lightweight on its own and
 * instead of containing a large amount of logic, it should only instead
 * contain methods specifying where a certain service should be loaded from.
 * When extending the Hyde core, create a new ServiceProvider extending the
 * HydeServiceProvider, and register your HydeManagerContract implementation.
 * Then in the app config, swap out the default ServiceProvider with your own.
 */
interface HydeManagerContract
{
    /**
     * Get the fully qualified class name of the SourceLocationManager implementation.
     * @see \Hyde\Framework\Core\SourceLocationManager
     */
    public function getSourceLocationManager(): string;
}