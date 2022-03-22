<?php

namespace Hyde\Framework\Actions;

use Hyde\Framework\Hyde;
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use Torchlight\Commonmark\V2\TorchlightExtension;

/**
 * Converts Markdown into HTML
 */
class MarkdownConverter
{
    /**
     * Parse the Markdown into HTML.
     *
     * @param string $markdown
     * @return string $html
     */
    public static function parse(string $markdown): string
    {
        $converter = new CommonMarkConverter();

        $converter->getEnvironment()->addExtension(new GithubFlavoredMarkdownExtension());

        if (Hyde::hasTorchlight()) {
            $converter->getEnvironment()->addExtension(new TorchlightExtension());
        }

        $html = $converter->convert($markdown);

        if (Hyde::hasTorchlight()
            && config('torchlight.attribution.enabled', true)
            && str_contains($html, 'Syntax highlighted by torchlight.dev')) {
            $html .= $converter->convert(config('torchlight.attribution.markdown', 'Syntax highlighted by torchlight.dev'));
        }

        return $html;
    }
}
