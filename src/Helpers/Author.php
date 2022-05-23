<?php

namespace Hyde\Framework\Helpers;

use Hyde\Framework\Models\Author as AuthorModel;

class Author
{
	static function create(string $username, ?string $display_name = null, ?string $website = null): AuthorModel
	{
		return new AuthorModel($username, [
			'name' => $display_name,
			'website '=> $website
		]);
	}
}