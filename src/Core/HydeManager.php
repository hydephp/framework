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
    protected HydeSystemManager $hydeSystemManager;

    public function __construct()
    {
        $this->bootstrap();
    }

    public function bootstrap(): void
    {
        $this->hydeSystemManager = new ($this->getHydeSystemManager());
    }

    public function getHydeSystemManager(): string
    {
        return HydeSystemProvider::class;
    }

    public function hydeSystemManager(): HydeSystemManager
    {
        return $this->hydeSystemManager;
    }

    /**
     * Get the instantiated provider from the service container for the given contract.
     */
    public static function get(string $classImplementingContract)
    {
        return app()->get(HydeManagerContract::class)->{basename($classImplementingContract)}();
    }
}
