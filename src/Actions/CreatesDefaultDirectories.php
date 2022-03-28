<?php

namespace Hyde\Framework\Actions;

use Hyde\Framework\Hyde;

/**
 * Create the default directories required by the Application.
 *
 * To prevent any issues with file generations, this action
 * is automatically whenever the application is booted.
 *
 * This behavior is handled in the Service Provider.
 *
 * @see Hyde\Framework\HydeServiceProvider
 */
class CreatesDefaultDirectories
{
    /**
     * The directories required by the application.
     *
     * @var array
     */
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

    /**
     * Execute the action.
     *
     * @return void
     */
    public function __invoke(): void
    {
        foreach ($this->requiredDirectories as $directory) {
            // Does the directory exist?      // Otherwise, create it.
            is_dir(Hyde::path($directory)) || mkdir(Hyde::path($directory), recursive: true);
        }
    }

    /**
     * Retrieve the array of default directories.
     *
     * @return array
     */
    public static function getRequiredDirectories(): array
    {
        return (new CreatesDefaultDirectories)->requiredDirectories;
    }
}
