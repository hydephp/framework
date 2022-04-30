<?php

namespace Hyde\Framework\Concerns;

use Hyde\Framework\Models\Author;
use Hyde\Framework\Services\AuthorService;

/**
 * Handle logic for Page models that have an Author.
 *
 * @see \Hyde\Framework\Models\Author
 * @see \Tests\Unit\HasAuthorTest
 */
trait HasAuthor
{
    public Author $author;

    public function constructAuthor(): void
    {
        if (isset($this->matter['author'])) {
            if (is_string($this->matter['author'])) {
                $this->author = $this->findAuthor($this->matter['author']);
            }
            if (is_array($this->matter['author'])) {
                $this->author = $this->createAuthor($this->matter['author']);
            }
        }
    }

    protected function findAuthor(string $author): Author
    {
        return AuthorService::find($author) ?: new Author($author);
    }

    protected function createAuthor(array $data): Author
    {
        $username = $data['username'] ?? $data['name'] ?? 'Guest';

        return new Author($username, $data);
    }
}
