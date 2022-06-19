<?php

namespace Hyde\Testing\Framework\Unit;

use Hyde\Framework\Concerns\HasAuthor;
use Hyde\Framework\Models\Author;
use Hyde\Testing\TestCase;

/**
 * Class HasAuthorTest.
 *
 * @covers \Hyde\Framework\Concerns\HasAuthor
 */
class HasAuthorTest extends TestCase
{
    use HasAuthor;

    protected array $matter;

    public function test_it_can_create_a_new_author_instance_from_username_string()
    {
        $this->matter = [
            'author' => 'John Doe',
        ];

        $this->constructAuthor();
        $this->assertInstanceOf(Author::class, $this->author);
        $this->assertEquals('John Doe', $this->author->username);
        $this->assertNull($this->author->name);
        $this->assertNull($this->author->website);
    }

    public function test_it_can_create_a_new_author_instance_from_user_array()
    {
        $this->matter['author'] = [
            'username' => 'john_doe',
            'name' => 'John Doe',
            'website' => 'https://example.com',
        ];
        $this->constructAuthor();
        $this->assertInstanceOf(Author::class, $this->author);
        $this->assertEquals('john_doe', $this->author->username);
        $this->assertEquals('John Doe', $this->author->name);
        $this->assertEquals('https://example.com', $this->author->website);
    }
}
