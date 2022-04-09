<?php

namespace Hyde\Framework\Actions;

use Hyde\Framework\Hyde;

/**
 * @deprecated use FileCacheService instead
 */
class ChecksIfPublishableFileDiffersFromSource implements ActionContract
{
    protected string $filepath;
    protected string $compareTo;

    /**
     * @param  string  $filepath  relative to Hyde installation to check.
     * @param  string|null  $compareTo  optionally specify the filepath to search for in the cache.
     *                                  Omit to search for a file with the same name as the supplied filepath.
     */
    public function __construct(string $filepath, ?string $compareTo = null)
    {
        $this->filepath = $filepath;
        $this->compareTo = $compareTo ?? $this->filepath;
    }

    /**
     ** Check if a publishable file has been modified.
     *
     * Useful to determine if it is safe to overwrite a file.
     *
     * @see https://github.com/hydephp/framework/issues/67
     *
     * @return bool|null true if file has been modified, false if not,
     *                   null if file does not exist in the cache.
     */
    public function execute(): bool|null
    {
        $cache = static::getFilecache();

        if (isset($cache[$this->compareTo])) {
            return $cache[$this->compareTo]['md5sum'] !== md5_file($this->filepath);
        }

        return null;
    }

    public static function getFilecache(): array
    {
        return json_decode(file_get_contents(Hyde::vendorPath('resources/data/filecache.json')), true);
    }
}
