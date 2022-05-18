<?php

namespace Hyde\Framework\Services;

use Hyde\Framework\Helpers\Features;
use Hyde\Framework\Hyde;
use Hyde\Framework\Models\BladePage;
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
            $collection = CollectionService::getSourceFileListForModel(BladePage::class);
            
            foreach ($collection as $page) {
                $urlItem = $this->xmlElement->addChild('url');
                $urlItem->addChild('loc', htmlentities(Hyde::uriPath(Hyde::pageLink($page . '.html'))));
                $urlItem->addChild('lastmod', htmlentities($this->getLastModDateForFileOrFallback(
                    Hyde::path(BladePage::$sourceDirectory.DIRECTORY_SEPARATOR.$page.'.blade.php')
                )));
                $urlItem->addChild('changefreq', 'daily');
            }
        }
        
        return $this;
    }
    
    
    public function getXML(): string
    {
        $this->xmlElement->addAttribute('processing_time_ms', (string) round((microtime(true) - $this->time_start) * 1000, 2));
        
        return $this->xmlElement->asXML();
    }
    
    public static function generateSitemap(): string
    {
        return (new static)->generate()->getXML();
    }
    
    public static function canGenerateSitemap(): bool
    {
        return (Hyde::uriPath() !== false);
    }
    
    protected function getLastModDateForFileOrFallback(string $filepath): string
    {
        return file_exists($filepath)
        ? date('c', filemtime($filepath))
        : date('c');
    }
}