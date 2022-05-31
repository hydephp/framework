<?php

namespace Hyde\Framework\Services;

use Hyde\Framework\Hyde;

/**
 * Markdown Post Processor to render Laravel Blade within Markdown files.
 * 
 * Works on a line-by-line basis by searching for a line starting with the directive.
 * 
 * @example: [Blade]: @include('path/to/view.blade.php')
 * @example: [Blade]: @php(echo 'Hello World!')
 * @example: [Blade]: {{ time() }}
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
        $this->output = $this->html;

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
}
