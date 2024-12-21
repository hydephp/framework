<?php

declare(strict_types=1);

namespace Hyde\Framework\Features\Blogging\Models;

use Stringable;
use Hyde\Facades\Author;
use Hyde\Facades\Config;
use Illuminate\Support\Collection;
use JetBrains\PhpStorm\Deprecated;

use function strtolower;
use function is_string;

/**
 * Object representation of a post author for the site.
 */
class PostAuthor implements Stringable
{
    /**
     * The username of the author.
     * This is the key used to find authors in the config.
     */
    public readonly string $username;

    /**
     * The display name of the author.
     */
    public readonly string $name;

    /**
     * The author's website URL.
     *
     * Could for example, be a Twitter page, website, or a hyperlink to more posts by the author.
     * Should be a fully qualified link, meaning it starts with http:// or https://.
     */
    public readonly ?string $website;

    /**
     * Construct a new Post Author object.
     *
     * If your input is in the form of an array, you may rather want to use the `getOrCreate` method.
     *
     * @param  string  $username
     * @param  string|null  $name
     * @param  string|null  $website
     */
    public function __construct(string $username, ?string $name = null, ?string $website = null)
    {
        $this->username = $username;
        $this->name = $name ?? $this->username;
        $this->website = $website;
    }

    /**
     * Dynamically get or create an author based on a username string or front matter array.
     *
     * @param  string|array{username?: string, name?: string, website?: string}  $data
     */
    public static function getOrCreate(string|array $data): static
    {
        if (is_string($data)) {
            return static::get($data);
        }

        return Author::create(static::findUsername($data), $data['name'] ?? null, $data['website'] ?? null);
    }

    /** Get an Author from the config, or create it. */
    public static function get(string $username): static
    {
        return static::all()->firstWhere('username', strtolower($username)) ?? Author::create($username);
    }

    /** @return \Illuminate\Support\Collection<string, \Hyde\Framework\Features\Blogging\Models\PostAuthor> */
    public static function all(): Collection
    {
        return (new Collection(Config::getArray('hyde.authors', [])))->mapWithKeys(function (self $author): array {
            return [strtolower($author->username) => $author];
        });
    }

    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * @deprecated This is not needed as the name property can be accessed directly.
     */
    #[Deprecated(reason: 'Use the name property instead.', replacement: '%class%->name')]
    public function getName(): string
    {
        return $this->name;
    }

    /** @param array{username?: string, name?: string, website?: string} $data */
    protected static function findUsername(array $data): string
    {
        return $data['username'] ?? $data['name'] ?? 'Guest';
    }
}
