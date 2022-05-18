<?php

namespace Hyde\Framework\Services;

use Hyde\Framework\Helpers\Features;
use Hyde\Framework\Hyde;
use Hyde\Framework\Models\BladePage;
use Hyde\Framework\Models\DocumentationPage;
use Hyde\Framework\Models\MarkdownPage;
use Hyde\Framework\Models\MarkdownPost;
use SimpleXMLElement;

/**
* @see \Tests\Feature\Services\SitemapServiceTest
* 
* @see https://www.sitemaps.org/protocol.html
*/
class SitemapService
{
    public SimpleXMLElement $xmlElement;
    
    public function __construct()
    {
        $this->time_start = microtime(true);
        
        $this->xmlElement = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="https://www.sitemaps.org/schemas/sitemap/0.9"></urlset>');
        $this->xmlElement->addAttribute('generator', 'HydePHP '.Hyde::version());
    }
    
    public function generate(): self
    {
        if (Features::hasBladePages()) {
            $this->addPageModelUrls(
                BladePage::class
            );
        }

        if (Features::hasMarkdownPages()) {
            $this->addPageModelUrls(
                MarkdownPage::class
            );
        }

        if (Features::hasBlogPosts()) {
            $this->addPageModelUrls(
                MarkdownPost::class,
                'posts/'
            );
        }

        if (Features::hasDocumentationPages()) {
            $this->addPageModelUrls(
                DocumentationPage::class,
                Hyde::docsDirectory().'/'
            );
        }
        
        return $this;
    }
    
    public function getXML(): string
    {
        $this->xmlElement->addAttribute('processing_time_ms', (string) round((microtime(true) - $this->time_start) * 1000, 2));
        
        return $this->xmlElement->asXML();
    }

    public function addPageModelUrls(string $pageClass, string $routePrefix = ''): void
    {
        $collection = CollectionService::getSourceFileListForModel($pageClass);

        foreach ($collection as $page) {
            $urlItem = $this->xmlElement->addChild('url');
            $urlItem->addChild('loc', htmlentities(Hyde::uriPath(Hyde::pageLink($routePrefix.$page . '.html'))));
            $urlItem->addChild('lastmod', htmlentities($this->getLastModDate($pageClass, $page)));
            $urlItem->addChild('changefreq', 'daily');
        }
    }

    protected function getLastModDate(string $pageClass, string $page): string
    {
        return date('c', filemtime(
            Hyde::path($pageClass::$sourceDirectory.DIRECTORY_SEPARATOR.$page.$pageClass::$fileExtension)
        ));
    }

    public static function generateSitemap(): string
    {
        return (new static)->generate()->getXML();
    }

    public static function canGenerateSitemap(): bool
    {
        return (Hyde::uriPath() !== false);
    }
}