<?php

namespace Hyde\Framework\Services;

use Hyde\Framework\Hyde;
use Hyde\Framework\Models\Author;
use Illuminate\Support\Collection;
use Symfony\Component\Yaml\Yaml;

/**
 * Contains service methods relating to blog post authors
 */
class AuthorService
{
    protected string $filepath;
    protected array $yaml;
    public Collection $authors;

    /**
     */
    public function __construct()
    {
        $this->filepath = $this->getFilepath();
        $this->yaml = $this->getYaml();
        $this->authors = $this->getAuthors();
    }

    /**
     * Returns the filepath of the Yaml file.
     *
     * If the file does not exist, it will be created.
     *
     * @return string
     */
    public function getFilepath(): string
    {
        $filepath = Hyde::path('_data/authors.yml');

        if (!file_exists($filepath)) {
            file_put_contents($filepath, <<<'EOF'
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
        
        return $filepath;
    }
    
    /**
     * Parse the Yaml file.
     *
     * If the Yaml cannot be parsed, it will back up the file and regenerate it.
     *
     * @return array
     */
    public function getYaml(): array
    {
        $yaml = Yaml::parse(file_get_contents($this->filepath));

        if (!isset($yaml['authors'])) {
            copy($this->filepath, $this->filepath . '.corrupted');
            unlink($this->filepath);
            $this->getFilepath();
            return $this->getYaml();
        }

        return $yaml;
    }

    /**
     * Use the Yaml array to parse and collect the Authors
     *
     * @return Collection
     */
    public function getAuthors(): Collection
    {
        $collection = new Collection();

        foreach ($this->yaml['authors'] as $username => $data) {
            $collection->push(new Author($username, $data));
        }

        return $collection;
    }

    /**
     * Find and retrieve an Author by their username.
     *
     * @param string $username
     * @return Author|false
     */
    public static function find(string $username): Author|false
    {
        $service = new self;
        return $service->authors->firstWhere('username', $username) ?? false;
    }
}