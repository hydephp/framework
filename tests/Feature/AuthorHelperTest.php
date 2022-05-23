<?php

namespace Tests\Feature;

use Tests\TestCase;
use Hyde\Framework\Helpers\Author as AuthorHelper;
use Hyde\Framework\Models\Author as AuthorModel;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Collection;

/**
 * Class AuthorHelperTest.
 *
 * @covers \Hyde\Framework\Helpers\Author
 */
class AuthorHelperTest extends TestCase
{
    // Test create method creates new Author model
    public function test_create_method_creates_new_author_model()
    {
        $author = AuthorHelper::create('mr_hyde');

        $this->assertInstanceOf(AuthorModel::class, $author);
    }

    // Test create method accepts all parameters
    public function test_create_method_accepts_all_parameters()
    {
        $author = AuthorHelper::create('mr_hyde', 'Mr Hyde', 'https://mrhyde.com');

        $this->assertEquals('mr_hyde', $author->username);
        $this->assertEquals('Mr Hyde', $author->name);
        $this->assertEquals('https://mrhyde.com', $author->website);
    }
    
    // Test all method returns empty Collection if no Authors are set in config

    // Test all method returns Collection with all Authors defined in config

    // Test get method returns config defined Author by username

    // Test get method returns new Author if username not found in config
}
