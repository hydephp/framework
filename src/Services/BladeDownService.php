<?php

namespace Hyde\Framework\Services;

use Illuminate\Support\Facades\Blade;

/**
 * Markdown Processor to render Laravel Blade within Markdown files.
 *
 * Works on a line-by-line basis by searching for a line starting with the directive.
 * The preprocessor expands the directive to an HTML comment. The post-processor parses it.
 *
 * @example: [Blade]: {{ time() }}
 * @example: [Blade]: @include('path/to/view.blade.php')
 *
 * @see \Tests\Feature\Services\BladeDownServiceTest
 */
class BladeDownService
{
    protected string $html;
    protected string $output;

    protected array $pageData = [];

    public function __construct(string $html, ?array $pageData = [])
    {
        $this->html = $html;
        $this->pageData = $pageData;
    }

    public function process(): self
    {
        $this->output = implode("\n", array_map(function ($line) {
            return $this->lineStartsWithDirective($line)
                ? $this->processLine($line)
                : $line;
        }, explode("\n", $this->html)));

        return $this;
    }

    public function get(): string
    {
        return $this->output;
    }

    public static function render(string $html, ?array $pageData = []): string
    {
        return (new static(static::preprocess($html), $pageData))->process()->get();
    }

    public static function preprocess(string $markdown): string
    {
        return implode("\n", array_map(function ($line) {
            return str_starts_with(strtolower($line), strtolower('[Blade]:'))
                ? '<!-- HYDE'.trim(htmlentities($line)).' -->'
                : $line;
        }, explode("\n", $markdown)));
    }

    protected function lineStartsWithDirective(string $line): bool
    {
        return str_starts_with(strtolower($line), '<!-- hyde[blade]:');
    }

    protected function processLine(string $line): string
    {
        return Blade::render(
            substr(substr(html_entity_decode($line), 18), 0, -4),
            $this->pageData
        );
    }
}