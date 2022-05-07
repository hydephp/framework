<?php

namespace Hyde\Framework\Services;

use Hyde\Framework\Contracts\DocumentationSidebarServiceContract;
use Hyde\Framework\Models\DocumentationSidebar;
use Hyde\Framework\Models\DocumentationSidebarItem;

/**
 * Service class to create and manage the sidebar collection object.
 * 
 * @see \Tests\Feature\Services\DocumentationSidebarServiceTest
 */
class DocumentationSidebarService implements DocumentationSidebarServiceContract
{
    /**
     * The sidebar object created and managed by the service instance.
     */
    protected DocumentationSidebar $sidebar;

    /**
     * Shorthand to create a new Sidebar object using default methods.
     */
    public static function get(): DocumentationSidebar
    {
        return ((new static)->createSidebar()->withoutIndex()->getSidebar()
        )->sortItems()->getCollection();
    }

    /**
     * Parse the _docs directory for sidebar items to create a new collection.
     */
    public function createSidebar(): self
    {
        $this->sidebar = new DocumentationSidebar();

        foreach ($this->getSidebarItems() as $slug) {
            $this->sidebar->addItem(
                $this->createSidebarItemFromSlug($slug)
            );
        }

        return $this;
    }

    /**
     * Get the sidebar object created and managed by the service instance.
     */
    public function getSidebar(): DocumentationSidebar
    {
        return $this->sidebar;
    }

    /**
     * Remove the index page from the sidebar collection.
     */
    protected function withoutIndex(): self
    {
        $this->sidebar = $this->sidebar->reject(function (DocumentationSidebarItem $item) {
            return $item->destination === 'index';
        });

        return $this;
    }

    /**
     * Get an array of source files to add to the sidebar.
     */
    protected function getSidebarItems(): array
    {
        return CollectionService::getDocumentationPageList();
    }

    /**
     * Generate a SidebarItem object from a source file referenced by its slug.
     */
    protected function createSidebarItemFromSlug(string $slug): DocumentationSidebarItem
    {
        return DocumentationSidebarItem::parseFromFile($slug);
    }
}