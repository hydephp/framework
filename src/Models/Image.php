<?php

namespace Hyde\Framework\Models;

use Hyde\Framework\Actions\FindsContentLengthForImageObject;
use Hyde\Framework\Hyde;

/**
 * Holds the information for an image.
 */
class Image
{
    // Core properties

    /**
     * The image's path (if it is stored locally).
     * Example: _media/image.jpg.
     *
     * @var string|null
     */
    public ?string $path;

    /**
     * The image's URI (if stored externally).
     * Example: https://example.com/media/image.jpg.
     *
     * Will override the path property if both are set.
     *
     * @var string|null
     */
    public ?string $uri;

    /**
     * The image's description. (Used for alt text for screen readers.)
     * You should always set this to provide accessibility.
     * Example: "This is an image of a cat sitting in a basket.".
     *
     * @var string|null
     */
    public ?string $description;

    /**
     * The image's title. (Shows a tooltip on hover.)
     * Example: "My Cat Archer".
     *
     * @var string|null
     */
    public ?string $title;

    // Extra metadata

    /**
     * The image's copyright.
     * Example: "Copyright (c) 2020 John Doe".
     *
     * @var string|null
     */
    public ?string $copyright;

    /**
     * The image's license name.
     * Example: "CC BY-NC-SA 4.0".
     *
     * @var string|null
     */
    public ?string $license;

    /**
     * The image's license URL.
     * Example: "https://creativecommons.org/licenses/by-nc-sa/4.0/".
     *
     * @var string|null
     */
    public ?string $licenseUrl;

    /**
     * The image's author.
     * Example: "John Doe".
     *
     * @var string|null
     */
    public ?string $author;

    /**
     * The image's source (for attribution).
     * Example: "https://unsplash.com/photos/example".
     *
     * @var string|null
     */
    public ?string $credit = null;

    public function __construct(array $data = [])
    {
        foreach ($data as $key => $value) {
            $this->{$key} = $value;
        }
    }

    public function getSource(): ?string
    {
        return $this->uri ?? $this->path ?? null;
    }

    public function getLink(?string $currentPage = ''): string
    {
        return Hyde::image($this->getSource() ?? '', $currentPage);
    }

    public function getContentLength(): int
    {
        return (new FindsContentLengthForImageObject($this))->execute();
    }

    public function getImageAuthorAttributionString(): ?string
    {
        if (isset($this->author)) {
            if (isset($this->credit)) {
                return '<span itemprop="creator" itemscope="" itemtype="http://schema.org/Person"><a href="'.e($this->credit).'" rel="author noopener nofollow" itemprop="url"><span itemprop="name">'.e($this->author).'</span></a></span>';
            } else {
                return '<span itemprop="creator" itemscope="" itemtype="http://schema.org/Person"><span itemprop="name">'.e($this->author).'</span></span>';
            }
        }

        return null;
    }

    public function getCopyrightString(): ?string
    {
        if (isset($this->copyright)) {
            return '<span itemprop="copyrightNotice">'.e($this->copyright).'</span>';
        }

        return null;
    }

    public function getLicenseString(): ?string
    {
        if (isset($this->license) && isset($this->licenseUrl)) {
            return '<a href="'.e($this->licenseUrl).'" rel="license nofollow noopener" itemprop="license">'.e($this->license).'</a>';
        }

        if (isset($this->license)) {
            return '<span itemprop="license">'.e($this->license).'</span>';
        }

        return null;
    }

    public function getFluentAttribution(): string
    {
        $attribution = [];

        $getImageAuthorAttributionString = $this->getImageAuthorAttributionString();
        if ($getImageAuthorAttributionString !== null) {
            $attribution[] = 'Image by '.$getImageAuthorAttributionString;
        }

        $getCopyrightString = $this->getCopyrightString();
        if ($getCopyrightString !== null) {
            $attribution[] = $getCopyrightString;
        }

        $getLicenseString = $this->getLicenseString();
        if ($getLicenseString !== null) {
            $attribution[] = 'License '.$getLicenseString;
        }

        return implode('. ', $attribution);
    }

    /**
     * Used in resources\views\components\post\image.blade.php to add meta tags with itemprop attributes.
     *
     * @return array
     */
    public function getMetadataArray(): array
    {
        $metadata = [];

        if (isset($this->description)) {
            $metadata['text'] = $this->description;
        }

        if (isset($this->title)) {
            $metadata['name'] = $this->title;
        }

        $metadata['url'] = $this->getSource();
        $metadata['contentUrl'] = $this->getSource();

        return $metadata;
    }
}
