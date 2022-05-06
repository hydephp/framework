<?php

namespace Hyde\Framework\Services;

use Hyde\Framework\Contracts\AssetServiceContract;
use Hyde\Framework\Hyde;

class AssetService implements AssetServiceContract
{
    /**
     * The default HydeFront version to load.
     *
     * @property string $version HydeFront SemVer Tag
     */
    public string $version = 'v1.7';

    public function version(): string
    {
        return config('hyde.cdnVersionOverride', $this->version);
    }

    public function stylePath(): string
    {
        return $this->cdnPathConstructor('hyde.css');
    }

    public function scriptPath(): string
    {
        return $this->cdnPathConstructor('hyde.js');
    }

    /**
     * @deprecated use constructCdnPath() instead
     */
    public function cdnPathConstructor(string $file): string
    {
        return $this->constructCdnPath($file);
    }

    public function constructCdnPath(string $file): string
    {
        return 'https://cdn.jsdelivr.net/npm/hydefront@'.$this->version().'/dist/'.$file;
    }

    public function hasMediaFile(string $file): bool
    {
        return file_exists(Hyde::path('_media').'/'.$file);
    }
}
