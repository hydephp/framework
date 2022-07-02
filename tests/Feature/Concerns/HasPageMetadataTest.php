<?php

namespace Hyde\Framework\Testing\Feature\Concerns;

use Hyde\Framework\Concerns\HasPageMetadata;
use Hyde\Framework\Contracts\AbstractPage;
use Hyde\Framework\Helpers\Meta;
use Hyde\Testing\TestCase;

/**
 * @covers \Hyde\Framework\Concerns\HasPageMetadata
 *
 * @see \Hyde\Framework\Testing\Unit\HasPageMetadataRssFeedLinkTest
 */
class HasPageMetadataTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['hyde.meta' => []]);
        config(['hyde.site_url' => null]);
        config(['hyde.pretty_urls' => false]);
        config(['hyde.generate_sitemap' => false]);
    }

    protected function makePage(): AbstractPage
    {
        return new class extends AbstractPage
        {
            use HasPageMetadata;

            public string $slug = 'foo';
            public static string $outputDirectory = '';
        };
    }

    public function test_get_canonical_url_returns_url_for_top_level_page()
    {
        $page = $this->makePage();
        config(['hyde.site_url' => 'https://example.com']);

        $this->assertEquals('https://example.com/foo.html', $page->getCanonicalUrl());
    }

    public function test_get_canonical_url_returns_pretty_url_for_top_level_page()
    {
        $page = $this->makePage();
        config(['hyde.site_url' => 'https://example.com']);
        config(['hyde.pretty_urls' => true]);

        $this->assertEquals('https://example.com/foo', $page->getCanonicalUrl());
    }

    public function test_get_canonical_url_returns_url_for_nested_page()
    {
        $page = new class extends AbstractPage
        {
            use HasPageMetadata;

            public string $slug = 'foo';
            public static string $outputDirectory = '';

            public function getCurrentPagePath(): string
            {
                return 'bar/'.$this->slug;
            }
        };
        config(['hyde.site_url' => 'https://example.com']);

        $this->assertEquals('https://example.com/bar/foo.html', $page->getCanonicalUrl());
    }

    public function test_get_canonical_url_returns_url_for_deeply_nested_page()
    {
        $page = new class extends AbstractPage
        {
            use HasPageMetadata;

            public string $slug = 'foo';
            public static string $outputDirectory = '';

            public function getCurrentPagePath(): string
            {
                return 'bar/baz/'.$this->slug;
            }
        };
        config(['hyde.site_url' => 'https://example.com']);

        $this->assertEquals('https://example.com/bar/baz/foo.html', $page->getCanonicalUrl());
    }

    public function test_can_use_canonical_url_returns_true_when_both_uri_path_and_slug_is_set()
    {
        $page = $this->makePage();
        config(['hyde.site_url' => 'https://example.com']);

        $this->assertTrue($page->canUseCanonicalUrl());
    }

    public function test_can_use_canonical_url_returns_false_no_conditions_are_met()
    {
        $page = new class extends AbstractPage
        {
            use HasPageMetadata;

            public string $slug;
            public static string $outputDirectory = '';
        };

        $this->assertFalse($page->canUseCanonicalUrl());
    }

    public function test_can_use_canonical_url_returns_false_when_only_one_condition_is_met()
    {
        $page = new class extends AbstractPage
        {
            use HasPageMetadata;

            public string $slug;
            public static string $outputDirectory = '';
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

    public function test_render_page_metadata_returns_string_with_merged_metadata()
    {
        $page = $this->makePage();
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

    public function test_render_page_metadata_only_adds_canonical_if_conditions_are_met()
    {
        $page = $this->makePage();

        $this->assertEquals(
            '',
            $page->renderPageMetadata()
        );
    }

    public function test_get_dynamic_metadata_only_adds_canonical_if_conditions_are_met()
    {
        $page = $this->makePage();

        $this->assertEquals(
            [],
            $page->getDynamicMetadata()
        );
    }

    public function test_get_dynamic_metadata_adds_canonical_url_when_conditions_are_met()
    {
        $page = $this->makePage();
        config(['hyde.site_url' => 'https://example.com']);

        config(['hyde.meta' => [
            Meta::name('foo', 'bar'),
        ]]);

        $this->assertEquals(['<link rel="canonical" href="https://example.com/foo.html" />'],
            $page->getDynamicMetadata()
        );
    }

    public function test_get_dynamic_metadata_adds_sitemap_link_when_conditions_are_met()
    {
        $page = new class extends AbstractPage
        {
            use HasPageMetadata;
        };
        config(['hyde.site_url' => 'https://example.com']);
        config(['hyde.generate_sitemap' => true]);

        $this->assertEquals(['<link rel="sitemap" type="application/xml" title="Sitemap" href="https://example.com/sitemap.xml" />'],
            $page->getDynamicMetadata()
        );
    }

    public function test_get_dynamic_metadata_does_not_add_sitemap_link_when_conditions_are_not_met()
    {
        $page = new class extends AbstractPage
        {
            use HasPageMetadata;
        };
        config(['hyde.site_url' => 'https://example.com']);
        config(['hyde.generate_sitemap' => false]);

        $this->assertEquals([],
            $page->getDynamicMetadata()
        );
    }

    public function test_has_twitter_title_in_config_returns_true_when_present_in_config()
    {
        config(['hyde.meta' => [
            Meta::name('twitter:title', 'foo'),
        ]]);

        $page = new class
        {
            use HasPageMetadata;
        };

        $this->assertTrue($page->hasTwitterTitleInConfig());
    }

    public function test_has_twitter_title_in_config_returns_false_when_not_present_in_config()
    {
        config(['hyde.meta' => []]);

        $page = new class
        {
            use HasPageMetadata;
        };

        $this->assertFalse($page->hasTwitterTitleInConfig());
    }

    public function test_has_open_graph_title_in_config_returns_true_when_present_in_config()
    {
        config(['hyde.meta' => [
            Meta::property('title', 'foo'),
        ]]);

        $page = new class
        {
            use HasPageMetadata;
        };

        $this->assertTrue($page->hasOpenGraphTitleInConfig());
    }

    public function test_has_open_graph_title_in_config_returns_false_when_not_present_in_config()
    {
        config(['hyde.meta' => []]);

        $page = new class
        {
            use HasPageMetadata;
        };

        $this->assertFalse($page->hasOpenGraphTitleInConfig());
    }

    public function test_get_dynamic_metadata_adds_twitter_and_open_graph_title_when_conditions_are_met()
    {
        config(['hyde.meta' => [
            Meta::name('twitter:title', 'foo'),
            Meta::property('title', 'foo'),
        ]]);

        $page = new class extends AbstractPage
        {
            use HasPageMetadata;

            public string $title = 'bar';
        };

        $this->assertEquals([
            '<meta name="twitter:title" content="HydePHP - bar" />',
            '<meta property="og:title" content="HydePHP - bar" />',
        ],
            $page->getDynamicMetadata()
        );
    }
}
