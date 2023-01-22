<?php

declare(strict_types=1);

namespace Hyde\Framework\Features\Blogging\Models;

use function array_flip;
use function array_key_exists;
use function config;
use Illuminate\Support\Facades\Http;
use function key;

class RemoteFeaturedImage extends FeaturedImage
{
    protected function setSource(string $source): string
    {
        // Here we can validate the source URL if we want.

        return $source;
    }

    public function getSource(): string
    {
        return $this->source;
    }

    public function getContentLength(): int
    {
        $headers = Http::withHeaders([
            'User-Agent' => config('hyde.http_user_agent', 'RSS Request Client'),
        ])->head($this->getSource())->headers();

        if (array_key_exists('Content-Length', $headers)) {
            return (int) key(array_flip($headers['Content-Length']));
        }

        // Here we could throw an exception if we want to be strict about this, or add a warning.

        return 0;
    }
}
