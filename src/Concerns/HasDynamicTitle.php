<?php

namespace Hyde\Framework\Concerns;

use Hyde\Framework\Hyde;
use function Hyde\Framework\Models\str_starts_with;

/**
 * Find and return the title to use for a Markdown Document.
 */
trait HasDynamicTitle
{
    public function findTitleForDocument(): string
    {
        if (isset($this->matter['title'])) {
            return $this->matter['title'];
        }

        return $this->findTitleTagInMarkdown($this->body)
            ?: Hyde::titleFromSlug($this->slug);
    }

    /**
     * Attempt to find the title based on the first H1 tag.
     */
    protected function findTitleTagInMarkdown(string $stream): string|false
    {
        $lines = explode("\n", $stream);

        foreach ($lines as $line) {
            if (str_starts_with($line, '# ')) {
                return trim(substr($line, 2), ' ');
            }
        }

        return false;
    }
}
