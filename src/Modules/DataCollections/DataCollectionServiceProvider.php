<?php

namespace Hyde\Framework\Modules\DataCollections;

use Hyde\Framework\Hyde;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

/**
 * @see \Hyde\Framework\Testing\Feature\DataCollectionTest
 */
class DataCollectionServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Register the class alias
        AliasLoader::getInstance()->alias(
            'MarkdownCollection',
            Facades\MarkdownCollection::class
        );
    }

    public function boot()
    {
        // Create the _data directory if it doesn't exist
        if (! is_dir(Hyde::path('_data'))) {
            mkdir(Hyde::path('_data'));
        }
    }
}
