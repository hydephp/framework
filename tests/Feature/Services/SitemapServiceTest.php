<?php

namespace Tests\Feature\Services;

use Hyde\Framework\Hyde;
use Hyde\Framework\Services\SitemapService;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

/**
 * @covers \Hyde\Framework\Services\SitemapService
 */
class SitemapServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        File::deleteDirectory(Hyde::path('_pages'));
        File::makeDirectory(Hyde::path('_pages'));
        Hyde::copy(Hyde::vendorPath('resources/views/homepages/welcome.blade.php'), Hyde::path('_pages/index.blade.php'));
        Hyde::copy(Hyde::vendorPath('resources/views/pages/404.blade.php'), Hyde::path('_pages/404.blade.php'));
    }

    public function test_service_instantiates_xml_element()
    {
        $service = new SitemapService();
        $this->assertInstanceOf('SimpleXMLElement', $service->xmlElement);
    }

    public function test_generate_adds_default_pages_to_xml()
    {
        $service = new SitemapService();
        $service->generate();

        // Test runner has an index and 404 page, so we are using that as a baseline
        $this->assertCount(2, $service->xmlElement->url);
    }

    public function test_generate_adds_markdown_pages_to_xml()
    {
        touch(Hyde::path('_pages/foo.md'));

        $service = new SitemapService();
        $service->generate();

        $this->assertCount(3, $service->xmlElement->url);

        unlink(Hyde::path('_pages/foo.md'));
    }

    public function test_generate_adds_markdown_posts_to_xml()
    {
        touch(Hyde::path('_posts/foo.md'));

        $service = new SitemapService();
        $service->generate();

        $this->assertCount(3, $service->xmlElement->url);

        unlink(Hyde::path('_posts/foo.md'));
    }

    public function test_generate_adds_documentation_pages_to_xml()
    {
        touch(Hyde::path('_docs/foo.md'));

        $service = new SitemapService();
        $service->generate();

        $this->assertCount(3, $service->xmlElement->url);

        unlink(Hyde::path('_docs/foo.md'));
    }

    public function test_get_xml_returns_xml_string()
    {
        $service = new SitemapService();
        $service->generate();
        $xml = $service->getXML();

        $this->assertIsString($xml);
        $this->assertStringStartsWith('<?xml version="1.0" encoding="UTF-8"?>', $xml);
    }

    public function test_generate_sitemap_shorthand_method_returns_xml_string()
    {
        $xml = SitemapService::generateSitemap();

        $this->assertIsString($xml);
        $this->assertStringStartsWith('<?xml version="1.0" encoding="UTF-8"?>', $xml);
    }

    public function test_can_generate_sitemap_helper_returns_true_if_hyde_has_base_url()
    {
        config(['hyde.site_url' => 'foo']);
        $this->assertTrue(SitemapService::canGenerateSitemap());
    }

    public function test_can_generate_sitemap_helper_returns_false_if_hyde_does_not_have_base_url()
    {
        config(['hyde.site_url' => '']);
        $this->assertFalse(SitemapService::canGenerateSitemap());
    }

    public function test_can_generate_sitemap_helper_returns_false_if_sitemaps_are_disabled_in_config()
    {
        config(['hyde.site_url' => 'foo']);
        config(['hyde.generateSitemap' => false]);
        $this->assertFalse(SitemapService::canGenerateSitemap());
    }

    public function test_url_item_is_generated_correctly()
    {
        config(['hyde.pretty_urls' => false]);
        config(['hyde.site_url' => 'https://example.com']);
        touch(Hyde::path('_pages/0-test.blade.php'));

        $service = new SitemapService();
        $service->generate();

        $url = $service->xmlElement->url[0];
        $this->assertEquals('https://example.com/0-test.html', $url->loc);
        $this->assertEquals('daily', $url->changefreq);
        $this->assertEquals(date('c'), $url->lastmod);

        unlink(Hyde::path('_pages/0-test.blade.php'));
    }

    public function test_url_item_is_generated_with_pretty_ur_ls_if_enabled()
    {
        config(['hyde.pretty_urls' => true]);
        config(['hyde.site_url' => 'https://example.com']);
        touch(Hyde::path('_pages/0-test.blade.php'));

        $service = new SitemapService();
        $service->generate();

        $url = $service->xmlElement->url[0];
        $this->assertEquals('https://example.com/0-test', $url->loc);

        unlink(Hyde::path('_pages/0-test.blade.php'));
    }
}
