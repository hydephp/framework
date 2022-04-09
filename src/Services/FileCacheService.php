<?php

namespace Hyde\Framework\Services;

use Hyde\Framework\Hyde;

/**
 * Helper methods to interact with the filecache.json file.
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
            $checksums[] = $file['md5sum'];
        }

        return $checksums;
    }

    public static function checksumMatchesAny(string $checksum): bool
    {
        return in_array($checksum, static::getChecksums());
    }
}
