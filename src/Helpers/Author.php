<?php

namespace Hyde\Framework\Helpers;

use Hyde\Framework\Models\Author as AuthorModel;
use Illuminate\Support\Collection;

/**
 * @see \Tests\Feature\AuthorHelperTest
 */
class Author
{
	static function create(string $username, ?string $display_name = null, ?string $website = null): AuthorModel
	{
		return new AuthorModel($username, [
			'name' => $display_name,
			'website'=> $website
		]);
	}

	static function all(): Collection
	{
		return new Collection(config('authors', []));
	}

	static function get(string $username): AuthorModel
	{
		return static::all()->firstWhere('username', $username)
			?? static::create($username);
	}
}