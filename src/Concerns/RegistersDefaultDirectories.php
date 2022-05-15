<?php

namespace Hyde\Framework\Concerns;

trait RegistersDefaultDirectories
{
    /**
     * Register the default directories.
     *
     * @return void
     */
    protected function registerDefaultDirectories(array $directoryMapping)
    {
        foreach ($directoryMapping as $class => $location) {
            $class::$sourceDirectory = $location;
        }
    }
}