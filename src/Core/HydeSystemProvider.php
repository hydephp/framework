<?php

namespace Hyde\Framework\Core;

class HydeSystemProvider implements HydeSystemManager
{
    /**
     * @inheritDoc
     */
    public function getProjectRoot(): string
    {
        return getcwd();
    }

    public function shouldPublishDefaultDirectories(): bool
    {
        return true;
    }
}
