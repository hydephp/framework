<?php

namespace Hyde\Framework\Models;

/**
 * The Post Author Object Model.
 *
 * The Author is parsed from the authors.yml file using the AuthorService class
 */
class Author
{
    /**
     * The username (slug) of the author.
     *
     * @var string
     */
    public string $username;

    /**
     * The display name of the author.
     *
     * @var string|null
     */
    public ?string $name = null;

    /**
     * The author's website URI.
     *
     * Could for example, be a Twitter page, website,
     * or a hyperlink to more posts by the author.
     *
     * @var string|null
     */
    public ?string $website = null;

    /**
     * @param  string  $username
     * @param  array|null  $data
     */
    public function __construct(string $username, ?array $data = [])
    {
        $this->username = $username;
        if (isset($data['name'])) {
            $this->name = $data['name'];
        }
        if (isset($data['website'])) {
            $this->website = $data['website'];
        }
    }
}
