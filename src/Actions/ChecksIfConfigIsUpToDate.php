<?php

namespace Hyde\Framework\Actions;

use Hyde\Framework\Contracts\ActionContract;
use Hyde\Framework\Hyde;

/**
 * Checks if the installed config is up-to-date with the Framework's config.
 * Works by comparing the number of title blocks, which is a crude but fast way to check.
 *
 * @see \Hyde\Testing\Framework\Feature\Actions\ChecksIfConfigIsUpToDateTest
 */
class ChecksIfConfigIsUpToDate implements ActionContract
{
    /**
     * Cache result for the application lifecycle.
     */
    public static ?bool $isUpToDate = null;

    public function execute(): bool
    {
        if (static::$isUpToDate === null) {
            static::$isUpToDate = $this->isUpToDate();
        }

        return static::$isUpToDate;
    }

    protected function isUpToDate(): bool
    {
        return $this->findOptions(
            file_get_contents(Hyde::path('config/hyde.php'))
        ) === $this->findOptions(
            file_get_contents(Hyde::vendorPath('config/hyde.php'))
        );
    }

    protected function findOptions(string $config): int
    {
        return substr_count($config, '--------------------------------------------------------------------------');
    }
}
