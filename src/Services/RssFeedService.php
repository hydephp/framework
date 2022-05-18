<?php

namespace Hyde\Framework\Services;

use Hyde\Framework\Models\MarkdownPost;
use SimpleXMLElement;
use Hyde\Framework\Hyde;

/**
 * @see \Tests\Feature\Services\RssFeedServiceTest
 *
 * @see https://validator.w3.org/feed/docs/rss2.html
 */
class RssFeedService
{
    public SimpleXMLElement $feed;
    protected float $time_start;

    public function __construct()
    {
        $this->time_start = microtime(true);

        $this->feed = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><rss version="2.0" />');
        $this->feed->addChild('channel');

        $this->addInitialChannelItems();
    }

    public function generate(): self
    {
        foreach (Hyde::getLatestPosts() as $post) {
            $this->addItem($post);
        }

        return $this;
    }

    public function getXML(): string
    {
        $this->feed->channel->generator->addAttribute('hyde:processing_time_ms', (string) round((microtime(true) - $this->time_start) * 1000, 2), 'hyde');

        return $this->feed->asXML();
    }

    protected function addItem(MarkdownPost $post): void
    {
        $item = $this->feed->channel->addChild('item');
        $item->addChild('title', $post->findTitleForDocument());
        $item->addChild('link', $post->getCanonicalLink());
        $item->addChild('description', $post->getPostDescription());

        $this->addAdditionalItemData($item, $post);
    }

    protected function addAdditionalItemData(SimpleXMLElement $item, MarkdownPost $post): void
    {
        if (isset($post->date)) {
            $item->addChild('pubDate', $post->date->dateTimeObject->format(DATE_RSS));
        }

        if (isset($post->author)) {
            $item->addChild('dc:creator', $post->author->getName(), 'http://purl.org/dc/elements/1.1/');
        }
    }

    protected function addInitialChannelItems(): void
    {
        $this->feed->channel->addChild('title', $this->getTitle());
        $this->feed->channel->addChild('link', $this->getLink());
        $this->feed->channel->addChild('description', $this->getDescription());

        $this->addAdditionalChannelData();
    }

    protected function addAdditionalChannelData(): void
    {
        $this->feed->channel->addChild('language', config('hyde.language', 'en'));
        $this->feed->channel->addChild('generator', 'HydePHP '.Hyde::version());
        $this->feed->channel->addChild('lastBuildDate', date(DATE_RSS));
    }

    protected function getTitle(): string
    {
        return $this->xmlEscape(
            config('hyde.name', 'HydePHP')
        );
    }

    protected function getLink(): string
    {
        return $this->xmlEscape(
            config('hyde.site_url') ?? 'http://localhost'
        );
    }

    protected function getDescription(): string
    {
        return $this->xmlEscape(
            config('hyde.rssDescription',
                $this->getTitle() . ' RSS Feed')
        );
    }

    protected function xmlEscape(string $string): string
    {
        return htmlspecialchars($string, ENT_XML1 | ENT_COMPAT, 'UTF-8');
    }
}

