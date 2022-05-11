<?php

namespace Hyde\Framework\Concerns;

use Hyde\Framework\Models\DocumentationSidebar;

/**
 * Extracts logic for the sidebar categories used in the SidebarService.
 * @see \Hyde\Framework\Services\DocumentationSidebarService
 */
trait HasDocumentationSidebarCategories
{
    protected array $categories = [];

    public function hasCategories(): bool
    {
        $this->assembleCategories();

        return !empty($this->categories);
    }

    public function getCategories(): array
    {
        $this->assembleCategories();

        return $this->categories;
    }

    public function getItemsInCategory(string $category): DocumentationSidebar
    {
        // @todo: Implement getItemsInCategory() method.
        return new DocumentationSidebar();
    }

    protected function assembleCategories(): void
    {
        foreach ($this->sidebar as $item) {
            if (isset($item->category)) {
                // Add to the categories array if it doesn't exist.
                if (! in_array($item->category, $this->categories)) {
                    $this->categories[] = $item->category;
                }
            }
        }
    }
}
