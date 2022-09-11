<?php

namespace Hyde\Framework\Testing\Unit;

use Hyde\Framework\Contracts\FrontMatter\BlogPostSchema;
use Hyde\Framework\Contracts\FrontMatter\DocumentationPageSchema;
use Hyde\Framework\Contracts\FrontMatter\PageSchema;
use Hyde\Testing\TestCase;

/**
 * A state test to ensure the schemas can't be changed without breaking the tests.
 * This requires contributors to consider the impact of their changes as schema changes are rarely backwards compatible.
 *
 * @see \Hyde\Framework\Contracts\FrontMatter\PageSchema
 * @see \Hyde\Framework\Contracts\FrontMatter\BlogPostSchema
 * @see \Hyde\Framework\Contracts\FrontMatter\DocumentationPageSchema
 */
class SchemaContractsTest extends TestCase
{
    public function testSchemasAreNotAccidentallyChanged()
    {
        $this->assertEquals([
            'title'         => 'string',
            'navigation'    => 'array|navigation',
            'canonicalUrl'  => 'string|url',
        ], PageSchema::PAGE_SCHEMA);

        $this->assertEquals([
            'label'     => 'string',
            'hidden'    => 'bool',
            'priority'  => 'int',
        ], PageSchema::NAVIGATION_SCHEMA);

        $this->assertEquals([
            'title'        => 'string',
            'description'  => 'string',
            'category'     => 'string',
            'date'         => 'string',
            'author'       => 'string|array|author',
            'image'        => 'string|array|featured_image',
        ], BlogPostSchema::MARKDOWN_POST_SCHEMA);

        $this->assertEquals([
            'name'      => 'string',
            'username'  => 'string',
            'website'   => 'string|url',
        ], BlogPostSchema::AUTHOR_SCHEMA);

        $this->assertEquals([
            'path'         => 'string',
            'uri'          => 'string',
            'description'  => 'string',
            'title'        => 'string',
            'copyright'    => 'string',
            'license'      => 'string',
            'licenseUrl'   => 'string',
            'author'       => 'string',
            'credit'       => 'string',
        ], BlogPostSchema::FEATURED_IMAGE_SCHEMA);

        $this->assertEquals([
            'category'  => 'string',
            'navigation'    => 'array|navigation',
        ], DocumentationPageSchema::DOCUMENTATION_PAGE_SCHEMA);
    }
}
