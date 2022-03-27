<?php

namespace Hyde\Framework\Services;

use Hyde\Framework\Hyde;
use Hyde\Framework\Models\Author;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Symfony\Component\Yaml\Yaml;

/**
 * Contains service methods relating to blog post authors.
 */
class AuthorService
{
    public string $filepath;
    public array $yaml = [];

    public Collection $authors;

    /**
     * Construct the class.
     */
    public function __construct()
    {
        $this->filepath = Hyde::path('_data/authors.yml');

        if (file_exists($this->filepath)) {
            $this->yaml = $this->getYaml();
            $this->authors = $this->getAuthors();
        } else {
            $this->authors = new Collection();
        }
    }

    /**
     * Returns the filepath of the Yaml file.
     *
     * If the file does not exist, it will be created.
     */
    public function publishFile()
    {
        file_put_contents($this->filepath, <<<'EOF'
# In this file you can declare custom authors.

# In the default example, `mr_hyde` is the username. 
# When setting the author to mr_hyde in a blog post,
# the data in the array will automatically be added.

authors:
  mr_hyde:
    name: Mr Hyde
    website: https://github.com/hydephp/hyde
EOF
        );
    }

    /**
     * Parse the Yaml file.
     *
     * @return array
     */
    public function getYaml(): array
    {
        if (! file_exists($this->filepath)) {
            return [];
        }
        $parsed = Yaml::parse(file_get_contents($this->filepath));
        if (! is_array($parsed)) {
            return [];
        }

        return $parsed;
    }

    /**
     * Use the Yaml array to parse and collect the Authors.
     *
     * @return Collection
     */
    public function getAuthors(): Collection
    {
        $collection = new Collection();

        if (isset($this->yaml['authors'])) {
            foreach ($this->yaml['authors'] as $username => $data) {
                $collection->push(new Author($username, $data));
            }
        }

        return $collection;
    }

    /**
     * Find and retrieve an Author by their username.
     *
     * @param  string  $username  of the Author to search for
     * @param  bool  $forgiving  should the search be fuzzy?
     * @return Author|false
     */
    public static function find(string $username, bool $forgiving = true): Author|false
    {
        $service = new self;
        if ($forgiving) {
            $username = Str::snake($username);
        }
        if (isset($service->authors)) {
            return $service->authors->firstWhere('username', $username) ?? false;
        }

        return false;
    }
}
