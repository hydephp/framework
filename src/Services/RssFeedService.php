<?php

namespace Hyde\Framework\Services;

use SimpleXMLElement;
use Hyde\Framework\Hyde;

/**
 * @see \Tests\Feature\Services\RssFeedServiceTest
 */
class RssFeedService
{
    public SimpleXMLElement $feed;
    protected float $time_start;

    public function __construct()
    {
        $this->time_start = microtime(true);

        $this->feed = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><rss version="2.0" />');
        $this->feed->addAttribute('generator', 'HydePHP '.Hyde::version());
        $this->feed->addChild('channel');

        $this->addInitialChannelItems();
    }

    public function generate(): self
    {
        // TODO: Implement generate() method.

        return $this;
    }

    public function getXML(): string
    {
        $this->feed->addAttribute('processing_time_ms', (string) round((microtime(true) - $this->time_start) * 1000, 2));

        return $this->feed->asXML();
    }

    protected function addInitialChannelItems(): void
    {
        $this->feed->channel->addChild('title', $this->getTitle());
        $this->feed->channel->addChild('link', $this->getLink());
        $this->feed->channel->addChild('description', $this->getDescription());
    }

    protected function getTitle(): string
    {
        return config('hyde.name', 'HydePHP');
    }

    protected function getLink(): string
    {
        return config('hyde.site_url') ?? 'http://localhost';
    }

    protected function getDescription(): string
    {
        return config('hyde.rssDescription',
            $this->getTitle() . ' RSS Feed');
    }
}

