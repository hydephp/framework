<?php

namespace Hyde\Framework\Core;

interface HydeSystemManager
{
    /**
     * Return the full path to the root of the application.
     * Used to assemble all file paths in the Hyde::path() method.
     */
    public function getProjectRoot(): string;

    /**
     * Should the default directories be created automatically?
     *
     * Useful when proxying a project or when exposing the Hyde API.
     * Note that builds may fail if the directories are not created.
     */
    public function shouldPublishDefaultDirectories(): bool;
}
