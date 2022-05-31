<?php

namespace Hyde\Framework\Services;

use Hyde\Framework\Hyde;
use Illuminate\Support\Facades\Blade;

/**
 * Markdown Post Processor to render Laravel Blade within Markdown files.
 * 
 * Works on a line-by-line basis by searching for a line starting with the directive.
 * The reason it's a post processor and not a pre-processor is so that it does not
 * interfere with the Markdown parser.
 * 
 * Note that optional supplied data is global to the entire file/page. 
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
        return (new static($html, $pageData))->process()->get();
	}

    protected function lineStartsWithDirective(string $line): bool
    {
        return str_starts_with(strtolower($line), '[blade]:');
    }

    protected function processLine(string $line): string
    {
        return Blade::render(substr($line, 8), $this->pageData);
    }
}
