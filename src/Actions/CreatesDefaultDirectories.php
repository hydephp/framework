<?php

namespace Hyde\Framework\Actions;

use Hyde\Framework\Hyde;

class CreatesDefaultDirectories
{
    protected array $requiredDirectories = [
        '_drafts',
        '_pages',
        '_posts',
        '_media',
        '_docs',
        '_data',
        '_site',
        '_site/posts',
        '_site/media',
        '_site/docs',
        'resources/views/pages',
    ];

    public function __invoke(): void
    {
        foreach ($this->requiredDirectories as $directory) {
            // Does the directory exist?      // Otherwise, create it.
            is_dir(Hyde::path($directory)) || mkdir(Hyde::path($directory), recursive: true);
        }
    }

    public static function getRequiredDirectories(): array
    {
        return (new CreatesDefaultDirectories)->requiredDirectories;
    }
}
