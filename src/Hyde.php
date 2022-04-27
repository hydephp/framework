<?php

namespace Hyde\Framework;

use Composer\InstalledVersions;
use Hyde\Framework\Services\Internal\AssetManager;
use Hyde\Framework\Services\Internal\FileHelpers;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * General interface for Hyde services.
 *
 * @author  Caen De Silva <caen@desilva.se>
 * @copyright 2022 Caen De Silva
 * @license MIT License
 *
 * @link https://hydephp.github.io/
 */
class Hyde
{
    use FileHelpers;
    use AssetManager;

    /**
     * Return the Composer Package Version.
     *
     * @return string
     */
    public static function version(): string
    {
        return InstalledVersions::getPrettyVersion('hyde/framework') ?: 'unreleased';
    }

    /**
     * Create a title from a kebab-case slug.
     *
     * @param  string  $slug
     * @return string $title
     */
    public static function titleFromSlug(string $slug): string
    {
        return Str::title(str_replace('-', ' ', ($slug)));
    }

    /**
     * Get a Laravel Collection of all Posts as MarkdownPost objects.
     *
     * @return \Illuminate\Support\Collection
     *
     * @throws \Exception if the posts' directory does not exist
     */
    public static function getLatestPosts(): Collection
    {
        $collection = new Collection();

        foreach (glob(Hyde::path('_posts/*.md')) as $filepath) {
            $collection->push((new MarkdownPostParser(basename($filepath, '.md')))->get());
        }

        return $collection->sortByDesc('matter.date');
    }
}
