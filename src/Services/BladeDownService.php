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
	//
}
