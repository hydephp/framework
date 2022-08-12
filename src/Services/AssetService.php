<?php

namespace Hyde\Framework\Services;

use Hyde\Framework\Contracts\AssetServiceContract;
use Hyde\Framework\Hyde;

/**
 * @see \Hyde\Framework\Facades\Asset
 */
class AssetService implements AssetServiceContract
{
    /**
     * The default HydeFront version to load.
     *
     * @property string $version HydeFront SemVer Tag
     */
    public string $version = 'v2.0';

    public function version(): string
    {
        return $this->version;
    }

    public function constructCdnPath(string $file): string
    {
        return 'https://cdn.jsdelivr.net/npm/hydefront@'.$this->version().'/dist/'.$file;
    }

    /**
     * Alias for constructCdnPath.
     */
    public function cdnLink(string $file): string
    {
        return $this->constructCdnPath($file);
    }

    public function hasMediaFile(string $file): bool
    {
        return file_exists(Hyde::path('_media').'/'.$file);
    }
}
