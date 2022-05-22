<?php

namespace Hyde\Framework\Concerns;

use Hyde\Framework\Contracts\AbstractPage;

trait RegistersDefaultDirectories
{
    /**
     * Register the default directories.
     *
     * @return void
     */
    protected function registerDefaultDirectories(array $directoryMapping): void
    {
        foreach ($directoryMapping as $class => $location) {
            /** @var AbstractPage $class */
            $class::$sourceDirectory = $location;
        }
    }
}
