<?php

namespace Tests\Feature\Concerns;

use Hyde\Framework\Concerns\HasPageMetadata;
use Hyde\Framework\Contracts\AbstractPage;
use Hyde\Framework\Helpers\Meta;
use Tests\TestCase;

/**
 * @covers \Hyde\Framework\Concerns\HasPageMetadata
 * @see \Tests\Unit\HasPageMetadataRssFeedLinkTest
 */
class HasPageMetadataTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['hyde.meta' => []]);
        config(['hyde.site_url' => null]);
        config(['hyde.prettyUrls' => false]);
        config(['hyde.generateSitemap' => false]);
    }

    public function testGetCanonicalUrlReturnsUrlForTopLevelPage()
    {
        $page = new class extends AbstractPage
        {
            use HasPageMetadata;

            public string $slug = 'foo';
        };
        config(['hyde.site_url' => 'https://example.com']);

        $this->assertEquals('https://example.com/foo.html', $page->getCanonicalUrl());
    }

    public function testGetCanonicalUrlReturnsPrettyUrlForTopLevelPage()
    {
        $page = new class extends AbstractPage
        {
            use HasPageMetadata;

            public string $slug = 'foo';
        };
        config(['hyde.site_url' => 'https://example.com']);
        config(['hyde.prettyUrls' => true]);

        $this->assertEquals('https://example.com/foo', $page->getCanonicalUrl());
    }

    public function testGetCanonicalUrlReturnsUrlForNestedPage()
    {
        $page = new class extends AbstractPage
        {
            use HasPageMetadata;

            public string $slug = 'foo';

            public function getCurrentPagePath(): string
            {
                return 'bar/'.$this->slug;
            }
        };
        config(['hyde.site_url' => 'https://example.com']);

        $this->assertEquals('https://example.com/bar/foo.html', $page->getCanonicalUrl());
    }

    public function testGetCanonicalUrlReturnsUrlForDeeplyNestedPage()
    {
        $page = new class extends AbstractPage
        {
            use HasPageMetadata;

            public string $slug = 'foo';

            public function getCurrentPagePath(): string
            {
                return 'bar/baz/'.$this->slug;
            }
        };
        config(['hyde.site_url' => 'https://example.com']);

        $this->assertEquals('https://example.com/bar/baz/foo.html', $page->getCanonicalUrl());
    }

    public function testCanUseCanonicalUrlReturnsTrueWhenBothUriPathAndSlugIsSet()
    {
        $page = new class extends AbstractPage
        {
            use HasPageMetadata;

            public string $slug = 'foo';
        };
        config(['hyde.site_url' => 'https://example.com']);

        $this->assertTrue($page->canUseCanonicalUrl());
    }

    public function testCanUseCanonicalUrlReturnsFalseNoConditionsAreMet()
    {
        $page = new class extends AbstractPage
        {
            use HasPageMetadata;

            public string $slug;
        };

        $this->assertFalse($page->canUseCanonicalUrl());
    }

    public function testCanUseCanonicalUrlReturnsFalseWhenOnlyOneConditionIsMet()
    {
        $page = new class extends AbstractPage
        {
            use HasPageMetadata;

            public string $slug;
        };
        config(['hyde.site_url' => 'https://example.com']);

        $this->assertFalse($page->canUseCanonicalUrl());

        $page = new class extends AbstractPage
        {
            use HasPageMetadata;

            public string $slug = 'foo';
        };
        config(['hyde.site_url' => null]);

        $this->assertFalse($page->canUseCanonicalUrl());
    }

    public function testRenderPageMetadataReturnsStringWithMergedMetadata()
    {
        $page = new class extends AbstractPage
        {
            use HasPageMetadata;

            public string $slug = 'foo';
        };
        config(['hyde.site_url' => 'https://example.com']);

        config(['hyde.meta' => [
            Meta::name('foo', 'bar'),
        ]]);

        $this->assertEquals(
            '<meta name="foo" content="bar">'."\n".
            '<link rel="canonical" href="https://example.com/foo.html" />',
            $page->renderPageMetadata()
        );
    }

    public function testRenderPageMetadataOnlyAddsCanonicalIfConditionsAreMet()
    {
        $page = new class extends AbstractPage
        {
            use HasPageMetadata;

            public string $slug = 'foo';
        };

        $this->assertEquals(
            '',
            $page->renderPageMetadata()
        );
    }

    public function testGetDynamicMetadataOnlyAddsCanonicalIfConditionsAreMet()
    {
        $page = new class extends AbstractPage
        {
            use HasPageMetadata;

            public string $slug = 'foo';
        };

        $this->assertEquals(
            [],
            $page->getDynamicMetadata()
        );
    }

    public function testGetDynamicMetadataAddsCanonicalUrlWhenConditionsAreMet()
    {
        $page = new class extends AbstractPage
        {
            use HasPageMetadata;

            public string $slug = 'foo';
        };
        config(['hyde.site_url' => 'https://example.com']);

        config(['hyde.meta' => [
            Meta::name('foo', 'bar'),
        ]]);

        $this->assertEquals(['<link rel="canonical" href="https://example.com/foo.html" />'],
            $page->getDynamicMetadata()
        );
    }



    public function testGetDynamicMetadataAddsSitemapLinkWhenConditionsAreMet()
    {
        $page = new class extends AbstractPage
        {
            use HasPageMetadata;
        };
        config(['hyde.site_url' => 'https://example.com']);
        config(['hyde.generateSitemap' => true]);

        $this->assertEquals(['<link rel="sitemap" type="application/xml" title="Sitemap" href="https://example.com/sitemap.xml" />'],
            $page->getDynamicMetadata()
        );
    }
}
