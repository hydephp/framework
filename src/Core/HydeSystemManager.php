<?php

namespace Hyde\Framework\Core;

interface HydeSystemManager
{
    /**
     * Return the full path to the root of the application.
     * Used to assemble all file paths in the Hyde::path() method.
     */
    public function getProjectRoot(): string;
}
