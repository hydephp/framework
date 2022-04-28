<?php

namespace Hyde\Framework\Services;

use Hyde\Framework\Contracts\AssetServiceContract;

class AssetService implements AssetServiceContract
{
    /**
     * The HydeFront version to load.
     * @property string $version
     */
    public string $version = 'v1.3.1';

    public function version(): string
    {
        return config('hyde.cdnHydeFrontVersionOverride', $this->version);
    }

    public function tailwindPath(): string|false
    {
        return config('hyde.loadTailwindFromCDN', false)
            ? $this->cdnPathConstructor('app.css')
            : false;
    }

    public function stylePath(): string
    {
        return $this->cdnPathConstructor('hyde.css');
    }

    public function scriptPath(): string
    {
        return $this->cdnPathConstructor('hyde.js');
    }

    public function cdnPathConstructor(string $file): string
    {
        return 'https://cdn.jsdelivr.net/gh/hydephp/hydefront@' . $this->version() . '/dist/' . $file;
    }
}