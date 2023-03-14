<?php

declare(strict_types=1);

namespace Hyde\Markdown\Processing;

use Hyde\Markdown\Contracts\MarkdownPostProcessorContract;
use Hyde\Markdown\Contracts\MarkdownPreProcessorContract;
use Illuminate\Support\Facades\Blade;

use function html_entity_decode;
use function strtolower;
use function array_map;
use function explode;
use function implode;
use function substr;
use function trim;
use function e;

/**
 * Markdown Processor to render Laravel Blade within Markdown files.
 *
 * Works on a line-by-line basis by searching for a line starting with the directive.
 * The preprocessor expands the directive to an HTML comment. The post-processor parses it.
 *
 * @example: [Blade]: {{ time() }}
 *
 * @example: [Blade]: @include('path/to/view.blade.php')
 *
 * @phpstan-consistent-constructor
 */
class BladeDownProcessor implements MarkdownPreProcessorContract, MarkdownPostProcessorContract
{
    protected string $html;
    protected string $output;

    protected array $pageData = [];

    public static function render(string $html, array $pageData = []): string
    {
        return (new static(static::preprocess($html), $pageData))->run()->get();
    }

    public static function preprocess(string $markdown): string
    {
        return implode("\n", array_map(function (string $line): string {
            return str_starts_with(strtolower($line), strtolower('[Blade]:'))
                ? '<!-- HYDE'.trim(e($line)).' -->'
                : $line;
        }, explode("\n", $markdown)));
    }

    public static function postprocess(string $html, array $pageData = []): string
    {
        return (new static($html, $pageData))->run()->get();
    }

    public function __construct(string $html, ?array $pageData = [])
    {
        $this->html = $html;
        $this->pageData = $pageData;
    }

    public function run(): static
    {
        $this->output = implode("\n", array_map(function (string $line): string {
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
