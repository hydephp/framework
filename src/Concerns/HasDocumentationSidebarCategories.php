<?php

namespace Hyde\Framework\Concerns;

use Hyde\Framework\Models\DocumentationSidebar;

/**
 * Extracts logic for the sidebar categories used in the SidebarService.
 * @see \Hyde\Framework\Services\DocumentationSidebarService
 */
trait HasDocumentationSidebarCategories
{
    public function hasCategories(): bool
    {
        // @todo: Implement hasCategories() method.
        return false;
    }

    public function getCategories(): array
    {
        // @todo: Implement getCategories() method.
        return [];
    }

    public function getItemsInCategory(string $category): DocumentationSidebar
    {
        // @todo: Implement getItemsInCategory() method.
        return new DocumentationSidebar();
    }
}
