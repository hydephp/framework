<?php

namespace Hyde\Framework\Core;

/**
 * The HydeManager bootstraps the Hyde Framework Core Services.
 *
 * To extend implementations, create a class that extends this class,
 * and register it as the Singleton implementation of the Contract in a
 * ServiceProvider extending the HydeServiceProvider and load it in app.php.
 */
class HydeManager implements HydeManagerContract
{
    protected SourceLocationManager $sourceLocationManager;

    public function __construct()
    {
        $this->bootstrap();
    }

    public function bootstrap(): void
    {
        $this->sourceLocationManager = new ($this->getSourceLocationManager());
    }

    public function getSourceLocationManager(): string
    {
        return SourceLocationProvider::class;
    }
}