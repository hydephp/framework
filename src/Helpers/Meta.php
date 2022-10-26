<?php

namespace Hyde\Framework\Helpers;

use Hyde\Framework\Modules\Metadata\GlobalMetadataBag;
use Hyde\Framework\Modules\Metadata\Models\LinkElement;
use Hyde\Framework\Modules\Metadata\Models\MetadataElement;
use Hyde\Framework\Modules\Metadata\Models\OpenGraphElement;

/**
 * Helpers to fluently declare HTML meta elements using their object representations.
 *
 * @see \Hyde\Framework\Testing\Feature\MetadataHelperTest
 */
class Meta
{
    /**
     * Create a new <meta> element class with the given name and content.
     *
     * @param  string  $name  The meta tag's name attribute.
     * @param  string  $content  The content of the meta tag.
     * @return \Hyde\Framework\Modules\Metadata\Models\MetadataElement
     *
     * @link https://www.w3schools.com/tags/tag_meta.asp
     */
    public static function name(string $name, string $content): MetadataElement
    {
        return new MetadataElement($name, $content);
    }

    /**
     * Create a new <meta> element class with the given OpenGraph property and content.
     *
     * @param  string  $property  The meta tag's property attribute. The "og:" prefix is optional.
     * @param  string  $content  The content of the meta tag.
     * @return \Hyde\Framework\Modules\Metadata\Models\OpenGraphElement
     *
     * @link https://ogp.me/
     */
    public static function property(string $property, string $content): OpenGraphElement
    {
        return new OpenGraphElement($property, $content);
    }

    /**
     * Create a new <link> element class with the given rel and href.
     *
     * @param  string  $rel  The link tag's rel attribute.
     * @param  string  $href  The link tag's href attribute.
     * @param  array  $attr  An optional key-value array of additional attributes.
     * @return \Hyde\Framework\Modules\Metadata\Models\LinkElement
     *
     * @link https://www.w3schools.com/tags/tag_link.asp
     */
    public static function link(string $rel, string $href, array $attr = []): LinkElement
    {
        return new LinkElement($rel, $href, $attr);
    }

    /**
     * Get the global metadata bag.
     */
    public static function get(): GlobalMetadataBag
    {
        return GlobalMetadataBag::make();
    }

    /**
     * Render the global metadata bag.
     *
     * @return string
     */
    public static function render(): string
    {
        return static::get()->render();
    }
}
