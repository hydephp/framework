<?php

namespace Hyde\Framework\Actions;

use Hyde\Framework\Hyde;
use JetBrains\PhpStorm\Pure;

/**
 * Creates and returns a list of markdown paths
 * @deprecated as it will be moved into a static method in the post class
 */
class GetMarkdownPostList
{
    /**
     * @return array
     */
    #[Pure] public function execute(): array
    {
        $array = [];

        foreach (glob(Hyde::path('_posts/*.md')) as $filepath) {
            $array[basename($filepath, '.md')] = $filepath;
        }

        return $array;
    }
}
