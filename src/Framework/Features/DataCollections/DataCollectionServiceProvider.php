<?php

declare(strict_types=1);

namespace Hyde\Framework\Features\DataCollections;

use Hyde\Facades\Features;
use Hyde\Framework\Features\DataCollections\Facades\MarkdownCollection;
use Hyde\Hyde;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

/**
 * @see \Hyde\Framework\Testing\Feature\DataCollectionTest
 */
class DataCollectionServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register the class alias
        AliasLoader::getInstance()->alias(
            'MarkdownCollection',
            MarkdownCollection::class
        );
    }

    public function boot(): void
    {
        if (Features::hasDataCollections()) {
            // Create the _data directory if it doesn't exist
            if (! is_dir(Hyde::path('_data'))) {
                mkdir(Hyde::path('_data'));
            }
        }
    }
}
