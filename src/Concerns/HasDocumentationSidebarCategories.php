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
        return $this->sidebar->filter(function ($item) use ($category) {
            return $item->category === $category;
        });
    }

    protected function assembleCategories(): void
    {
        foreach ($this->sidebar as $item) {
            if (isset($item->category)) {
                if (! in_array($item->category, $this->categories)) {
                    $this->categories[] = $item->category;
                }
            }
        }

        if (! empty($this->categories)) {
            $this->setCategoryOfUncategorizedItems();
        }

        // Todo sort by priority
    }
    
    protected function setCategoryOfUncategorizedItems(): void
    {
        foreach ($this->sidebar as $item) {
            if (! isset($item->category)) {
                $item->category = 'other';
            }
        }

        $this->categories[] = 'other';
    }
}
