<?php

namespace Hyde\Framework\Models;

use Hyde\Framework\Services\AuthorService;

/**
 * Trait HasAuthor
 *
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