<?php

namespace Hyde\Framework;

/**
 * Helpers to fluently declare HTML meta tags.
 *
 * @see \Tests\Feature\MetadataHelperTest
 */
class Meta
{
    public static function name(string $name, string $content, bool $ifConditionIsMet = true): string
    {
        return '<meta name="'.e($name).'" content="'.e($content).'">';
    }

    public static function property(string $property, string $content, bool $ifConditionIsMet = true): string
    {
        $property = static::formatOpenGraphProperty($property);

        return '<meta property="'.e($property).'" content="'.e($content).'">';
    }

    public static function render(array $overridesGlobalMeta = []): string
    {
        return implode("\n",
         static::filterUnique(
                array_merge(
                    static::getGlobalMeta(),
                    $overridesGlobalMeta
                )
            )
        );
    }

    protected static function filterUnique(array $meta): array
    {
        $array = [];
        $existing = [];

        foreach ($meta as $metaItem) {
            $substring = substr($metaItem, 6, strpos($metaItem, ' content="') - 6);

            if (! in_array($substring, $existing)) {
                $array[] = $metaItem;
                $existing[] = $substring;
            }
        }

        return $array;
    }

    public static function getGlobalMeta(): array
    {
        return config('hyde.meta', []);
    }

    protected static function formatOpenGraphProperty(string $property): string
    {
        return str_starts_with($property, 'og:') ? $property : 'og:'.$property;
    }
}
