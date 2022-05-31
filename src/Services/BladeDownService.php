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
 * @example: [Blade]: {{ time() }}
 * @example: [Blade]: @include('path/to/view.blade.php')
 * 
 * @see \Tests\Feature\Services\BladeDownServiceTest
 */
class BladeDownService
{
    protected string $html;
    protected string $output;

    public function __construct(string $html)
    {
        $this->html = $html;
    }

    public function process(): self
    {
        $this->output = implode("\n", array_map(function ($line) {
			if (str_starts_with($line, '[Blade]:')) {
				return $this->processLine($line);
			}
            return $line;
		}, explode("\n", $this->html)));

        return $this;
    }

    public function get(): string
    {
        return $this->output;
    }

    public static function render(string $html): string
    {
        return (new static($html))->process()->get();
	}

    protected function processLine(string $line): string
    {
        return Blade::render(substr($line, 8));
    }
}
