<?php

namespace Tests\Feature\Services\SitemapServiceTest;

use Hyde\Framework\Hyde;
use Hyde\Framework\Services\SitemapService;
use Tests\TestCase;

/**
 * @covers \Hyde\Framework\Services\SitemapService
 */
class SitemapServiceTest extends TestCase
{
    // Test service instantiates an XML element
    public function testServiceInstantiatesXmlElement()
    {
        $service = new SitemapService();
        $this->assertInstanceOf('SimpleXMLElement', $service->xmlElement);
    }

    // Test generate method adds default pages to sitemap XML
    public function testGenerateAddsDefaultPagesToXml()
    {
        $service = new SitemapService();
        $service->generate();

        // Test runner has an index and 404 page, so we are using that as a baseline
        $this->assertCount(2, $service->xmlElement->url);
    }

    // Test generate method adds Markdown pages to sitemap XML
    public function testGenerateAddsMarkdownPagesToXml()
    {
        touch(Hyde::path('_pages/foo.md'));

        $service = new SitemapService();
        $service->generate();

        $this->assertCount(3, $service->xmlElement->url);

        unlink(Hyde::path('_pages/foo.md'));
    }

    // Test generate method adds Markdown posts to sitemap XML
    public function testGenerateAddsMarkdownPostsToXml()
    {
        touch(Hyde::path('_posts/foo.md'));

        $service = new SitemapService();
        $service->generate();

        $this->assertCount(3, $service->xmlElement->url);

        unlink(Hyde::path('_posts/foo.md'));
    }

    // Test generate method adds documentation pages to sitemap XML
    public function testGenerateAddsDocumentationPagesToXml()
    {
        touch(Hyde::path('_docs/foo.md'));

        $service = new SitemapService();
        $service->generate();

        $this->assertCount(3, $service->xmlElement->url);

        unlink(Hyde::path('_docs/foo.md'));
    }

    // Test getXML method returns XML string
    public function testGetXMLReturnsXMLString()
    {
        $service = new SitemapService();
        $service->generate();
        $xml = $service->getXML();

        $this->assertIsString($xml);
        $this->assertStringStartsWith('<?xml version="1.0" encoding="UTF-8"?>', $xml);
    }

    // Test generateSitemap shorthand method returns XML string
    public function testGenerateSitemapShorthandMethodReturnsXMLString()
    {
        $xml = SitemapService::generateSitemap();

        $this->assertIsString($xml);
        $this->assertStringStartsWith('<?xml version="1.0" encoding="UTF-8"?>', $xml);
    }

    // Test canGenerateSitemap helper returns true if Hyde has a base URL
    public function testCanGenerateSitemapHelperReturnsTrueIfHydeHasBaseUrl()
    {
        config(['hyde.site_url' => 'foo']);
        $this->assertTrue(SitemapService::canGenerateSitemap());
    }

    // Test canGenerateSitemap helper returns false if Hyde does not have a base URL
    public function testCanGenerateSitemapHelperReturnsFalseIfHydeDoesNotHaveBaseUrl()
    {
        config(['hyde.site_url' => '']);
        $this->assertFalse(SitemapService::canGenerateSitemap());
    }

    // Test canGenerateSitemap helper returns false if sitemaps are disabled in config
    public function testCanGenerateSitemapHelperReturnsFalseIfSitemapsAreDisabledInConfig()
    {
        config(['hyde.site_url' => 'foo']);
        config(['hyde.generateSitemap' => false]);
        $this->assertFalse(SitemapService::canGenerateSitemap());
    }

    // Test URL item is generated correctly
    public function testURLItemIsGeneratedCorrectly()
    {
        config(['hyde.prettyUrls' => false]);
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

    // Test URL item is generated with pretty URLs if enabled
    public function testURLItemIsGeneratedWithPrettyURLsIfEnabled()
    {
        config(['hyde.prettyUrls' => true]);
        config(['hyde.site_url' => 'https://example.com']);
        touch(Hyde::path('_pages/0-test.blade.php'));

        $service = new SitemapService();
        $service->generate();

        $url = $service->xmlElement->url[0];
        $this->assertEquals('https://example.com/0-test', $url->loc);

        unlink(Hyde::path('_pages/0-test.blade.php'));
    }
}
