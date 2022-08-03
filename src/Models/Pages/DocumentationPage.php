<?php

namespace Hyde\Framework\Models\Pages;

use Hyde\Framework\Concerns\HasTableOfContents;
use Hyde\Framework\Contracts\AbstractMarkdownPage;
use Hyde\Framework\Contracts\RouteContract;
use Hyde\Framework\Models\Route;
use Illuminate\Support\Str;

class DocumentationPage extends AbstractMarkdownPage
{
    use HasTableOfContents;

    public static string $sourceDirectory = '_docs';
    public static string $outputDirectory = 'docs';
    public static string $template = 'hyde::layouts/docs';

    /**
     * The sidebar category group, if any.
     */
    public ?string $category;

    public function __construct(array $matter = [], string $body = '', string $title = '', string $slug = '')
    {
        parent::__construct($matter, $body, $title, $slug);
        $this->category = $this->getDocumentationPageCategory();
    }

    protected function getDocumentationPageCategory(): ?string
    {
        if (str_contains($this->slug, '/')) {
            return Str::before($this->slug, '/');
        }

        return $this->matter['category'] ?? null;
    }

    /** @inheritDoc */
    public function getCurrentPagePath(): string
    {
        return trim(static::getOutputDirectory().'/'.basename($this->slug), '/');
    }

    /** @internal */
    public function getOnlineSourcePath(): string|false
    {
        if (config('docs.source_file_location_base') === null) {
            return false;
        }

        return trim(config('docs.source_file_location_base'), '/').'/'.$this->slug.'.md';
    }

    public static function home(): ?RouteContract
    {
        return Route::exists('docs/index') ? Route::get('docs/index') : null;
    }

    public static function hasTableOfContents(): bool
    {
        return config('docs.table_of_contents.enabled', true);
    }
}
