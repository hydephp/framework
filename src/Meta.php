<?php

namespace Hyde\Framework;

class Meta
{
    public static function name(string $name, string $content, bool $ifConditionIsMet = true): ?string
    {
        if ($ifConditionIsMet) {
            return '<meta name="' . e($name) . '" content="' . e($content) . '">';
        }
        return null;
    }

    public static function property(string $property, string $content, bool $ifConditionIsMet = true): ?string
    {
        if ($ifConditionIsMet) {
            $property = static::formatOpenGraphProperty($property);
            return '<meta property="' . e($property) . '" content="' . e($content) . '">';
        }
        return null;
    }

    public static function render(array $overridesGlobalMeta = []): string
    {
        return implode("\n",
            array_merge(
                static::getGlobalMeta(),
                $overridesGlobalMeta
            )
        );
    }

    public static function getGlobalMeta(): array
    {
        return config('hyde.meta', []);
    }

    protected static function formatOpenGraphProperty(string $property): string
    {
        return str_starts_with('og:', $property) ? $property : 'og:' . $property;
    }
}