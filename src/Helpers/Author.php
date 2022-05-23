<?php

namespace Hyde\Framework\Helpers;

use Hyde\Framework\Models\Author as AuthorModel;

class Author
{
	static function create(string $username, ?string $displayName = null, ?string $website = null): AuthorModel
	{
		return new AuthorModel($username, [
			'name' => $displayName,
			'website '=> $website
		]);
	}
}