<?php

namespace Hyde\Framework\Core;

class HydeManager implements HydeManagerContract
{
    public function getSourceLocationManager(): string
    {
        return SourceLocationProvider::class;
    }
}