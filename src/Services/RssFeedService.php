<?php

namespace Hyde\Framework\Services;

use Hyde\Framework\Hyde;
use Hyde\Framework\Models\MarkdownPost;
use SimpleXMLElement;

/**
 * @see \Tests\Feature\Services\RssFeedServiceTest
 * @see https://validator.w3.org/feed/docs/rss2.html
 */
class RssFeedService
{
    public SimpleXMLElement $feed;

    public function __construct()
    {
        $this->feed = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?>
            <rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:dc="http://purl.org/dc/elements/1.1/" />');
        $this->feed->addChild('channel');

        $this->addInitialChannelItems();
    }

    public function generate(): self
    {
        /** @var MarkdownPost $post */
        foreach (Hyde::getLatestPosts() as $post) {
            $this->addItem($post);
        }

        return $this;
    }

    public function getXML(): string
    {
        return $this->feed->asXML();
    }

    protected function addItem(MarkdownPost $post): void
    {
        $item = $this->feed->channel->addChild('item');
        $item->addChild('title', $post->findTitleForDocument());
        $item->addChild('link', $post->getCanonicalLink());
        $item->addChild('guid', $post->getCanonicalLink());
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

        if (isset($post->category)) {
            $item->addChild('category', $post->category);
        }

        // Only support local images, as remote images would take extra time to make HTTP requests to get length
        if (isset($post->image) && isset($post->image->path)) {
            $image = $item->addChild('enclosure');
            $image->addAttribute('url', Hyde::uriPath(str_replace('_media', 'media', $post->image->path)));
            $image->addAttribute('type', str_ends_with($post->image->path, '.png') ? 'image/png' : 'image/jpeg');
            $image->addAttribute('length', filesize(Hyde::path($post->image->path)));
        }
    }

    protected function addInitialChannelItems(): void
    {
        $this->feed->channel->addChild('title', $this->getTitle());
        $this->feed->channel->addChild('link', $this->getLink());
        $this->feed->channel->addChild('description', $this->getDescription());

        $atomLink = $this->feed->channel->addChild('atom:link', namespace: 'http://www.w3.org/2005/Atom');
        $atomLink->addAttribute('href', $this->getLink().'/'.static::getDefaultOutputFilename());
        $atomLink->addAttribute('rel', 'self');
        $atomLink->addAttribute('type', 'application/rss+xml');

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
                $this->getTitle().' RSS Feed')
        );
    }

    protected function xmlEscape(string $string): string
    {
        return htmlspecialchars($string, ENT_XML1 | ENT_COMPAT, 'UTF-8');
    }

    public static function getDefaultOutputFilename(): string
    {
        return config('hyde.rssFilename', 'feed.rss');
    }

    public static function canGenerateFeed(): bool
    {
        return (Hyde::uriPath() !== false) && config('hyde.generateRssFeed', true);
    }
}
