<?php

namespace Hyde\Framework\Helpers;

use Hyde\Framework\Models\Author as AuthorModel;
use Illuminate\Support\Collection;

/**
 * @see \Tests\Feature\AuthorHelperTest
 */
class Author
{
    public static function create(string $username, ?string $display_name = null, ?string $website = null): AuthorModel
    {
        return new AuthorModel($username, [
            'name' => $display_name,
            'website'=> $website,
        ]);
    }

    public static function all(): Collection
    {
        return new Collection(config('authors', []));
    }

    public static function get(string $username): AuthorModel
    {
        return static::all()->firstWhere('username', $username)
            ?? static::create($username);
    }
}
