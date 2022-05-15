<?php

namespace Tests\Feature;

use Hyde\Framework\Models\MarkdownPost;
use Hyde\Framework\Services\BuildService;
use Tests\TestCase;

/**
 * Class SourceDirectoriesCanBeChangedTest.
 */
class SourceDirectoriesCanBeChangedTest extends TestCase
{
    public function test_post_directory_baseline()
    {
        $this->assertEquals('_posts', MarkdownPost::$sourceDirectory);
    }

    public function test_posts_directory_can_be_changed()
    {
        MarkdownPost::$sourceDirectory = '_source/posts';
        $this->assertEquals('_source/posts', MarkdownPost::$sourceDirectory);
    }

    public function test_autodiscovery_discovers_posts_in_changed_directory()
    {
        MarkdownPost::$sourceDirectory = '_source/posts';

        $this->assertEquals(
            '_source/posts',
            BuildService::getFilePathForModelClassFiles(MarkdownPost::class)
        );
    }
}
