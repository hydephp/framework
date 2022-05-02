<?php

namespace Hyde\Framework\Services;

use Hyde\Framework\Hyde;

/**
 * Helper methods to interact with the filecache.json file.
 *
 * @deprecated v0.20.0-dev, see https://github.com/hydephp/framework/issues/243
 */
class FileCacheService
{
    public static function getFilecache(): array
    {
        return json_decode(file_get_contents(Hyde::vendorPath('resources/data/filecache.json')), true);
    }

    public static function getChecksums(): array
    {
        $cache = static::getFilecache();

        $checksums = [];

        foreach ($cache as $file) {
            $checksums[] = $file['unixsum'];
        }

        return $checksums;
    }

    public static function checksumMatchesAny(string $checksum): bool
    {
        return in_array($checksum, static::getChecksums());
    }

    /**
     * A EOL agnostic wrapper for calculating MD5 checksums.
     *
     * @internal This function is not cryptographically secure.
     *
     * @see https://github.com/hydephp/framework/issues/85
     */
    public static function unixsum(string $string): string
    {
        $string = str_replace(["\r\n", "\r"], "\n", $string);

        return md5($string);
    }

    /* Shorthand for @see static::unixsum() but loads a file */
    public static function unixsumFile(string $file): string
    {
        return static::unixsum(file_get_contents($file));
    }
}
