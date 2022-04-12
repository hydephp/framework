<?php

namespace Hyde\Framework\Actions;

use Hyde\Framework\Hyde;

/**
 * Publishes default frontend resource files if they don't exist.
 *
 * This action is called when the Service Provider boots.
 *
 * @see \Hyde\Framework\HydeServiceProvider
 *
 * @todo Create option to disable the automatic behaviour.
 * @todo Create option to speficy usage of minified files.
 * @todo Create option here, or in the Hyde facade,
 *       to choose if resources should be loaded locally or from the CDN.
 */
class PublishesDefaultFrontendResourceFiles
{
    public static array $files = [
        'app.css',
        'hyde.css',
        'hyde.js',
    ];

    public function __construct(protected ?bool $force = false)
    {
        //
    }

    public function __invoke(): void
    {
        if (! is_dir(Hyde::path('resources/frontend'))) {
            mkdir(Hyde::path('resources/frontend'), 0755, true);
        }

        foreach (static::$files as $file) {
            $this->handleFile($file);
        }
    }

    protected function handleFile(string $file): void
    {
        Hyde::copy(
            Hyde::path('vendor/hyde/framework/resources/frontend/'.$file),
            Hyde::path('resources/frontend/'.$file),
            $this->force
        );
    }
}
