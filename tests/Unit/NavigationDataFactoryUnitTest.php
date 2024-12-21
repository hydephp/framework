<?php

declare(strict_types=1);

namespace Hyde\Framework\Testing\Unit;

use Hyde\Pages\MarkdownPage;
use Hyde\Testing\UnitTestCase;
use Hyde\Pages\DocumentationPage;
use Hyde\Markdown\Models\Markdown;
use Hyde\Markdown\Models\FrontMatter;
use Hyde\Framework\Factories\NavigationDataFactory;
use Hyde\Framework\Factories\Concerns\CoreDataObject;

/**
 * @covers \Hyde\Framework\Factories\NavigationDataFactory
 */
class NavigationDataFactoryUnitTest extends UnitTestCase
{
    protected function setUp(): void
    {
        self::resetKernel();
        self::mockConfig();
    }

    public function testSearchForPriorityInNavigationConfigForMarkdownPageWithKeyedConfig()
    {
        self::mockConfig(['hyde.navigation.order' => [
            'foo' => 15,
            'bar' => 10,
        ]]);

        $factory = new NavigationConfigTestClass($this->makeCoreDataObject(routeKey: 'foo'));
        $this->assertSame(15, $factory->makePriority());

        $factory = new NavigationConfigTestClass($this->makeCoreDataObject(routeKey: 'bar'));
        $this->assertSame(10, $factory->makePriority());
    }

    public function testSearchForPriorityInNavigationConfigForMarkdownPageWithListConfig()
    {
        self::mockConfig(['hyde.navigation.order' => [
            'foo',
            'bar',
        ]]);

        $factory = new NavigationConfigTestClass($this->makeCoreDataObject(routeKey: 'foo'));
        $this->assertSame(500, $factory->makePriority());

        $factory = new NavigationConfigTestClass($this->makeCoreDataObject(routeKey: 'bar'));
        $this->assertSame(501, $factory->makePriority());
    }

    public function testSearchForPriorityInNavigationConfigForMarkdownPageSupportsMixingKeyedAndListConfig()
    {
        self::mockConfig(['hyde.navigation.order' => [
            'foo',
            'bar' => 10,
            'baz',
        ]]);

        $factory = new NavigationConfigTestClass($this->makeCoreDataObject(routeKey: 'foo'));
        $this->assertSame(500, $factory->makePriority());

        $factory = new NavigationConfigTestClass($this->makeCoreDataObject(routeKey: 'bar'));
        $this->assertSame(10, $factory->makePriority());

        $factory = new NavigationConfigTestClass($this->makeCoreDataObject(routeKey: 'baz'));
        $this->assertSame(501, $factory->makePriority());

        $factory = new NavigationConfigTestClass($this->makeCoreDataObject(routeKey: 'qux'));
        $this->assertSame(999, $factory->makePriority());
    }

    public function testSearchForPriorityInNavigationConfigForDocumentationPageWithListConfig()
    {
        self::mockConfig(['docs.sidebar_order' => [
            'foo' => 15,
            'bar' => 10,
        ]]);

        $factory = new NavigationConfigTestClass($this->makeCoreDataObject('foo', pageClass: DocumentationPage::class));
        $this->assertSame(15, $factory->makePriority());

        $factory = new NavigationConfigTestClass($this->makeCoreDataObject('bar', pageClass: DocumentationPage::class));
        $this->assertSame(10, $factory->makePriority());
    }

    public function testSearchForPriorityInNavigationConfigForDocumentationPageWithKeyedConfig()
    {
        self::mockConfig(['docs.sidebar_order' => [
            'foo',
            'bar' => 10,
            'baz',
        ]]);

        $factory = new NavigationConfigTestClass($this->makeCoreDataObject('foo', pageClass: DocumentationPage::class));
        $this->assertSame(500, $factory->makePriority());

        $factory = new NavigationConfigTestClass($this->makeCoreDataObject('bar', pageClass: DocumentationPage::class));
        $this->assertSame(10, $factory->makePriority());

        $factory = new NavigationConfigTestClass($this->makeCoreDataObject('baz', pageClass: DocumentationPage::class));
        $this->assertSame(501, $factory->makePriority());

        $factory = new NavigationConfigTestClass($this->makeCoreDataObject('qux', pageClass: DocumentationPage::class));
        $this->assertSame(999, $factory->makePriority());
    }

    public function testSearchForPriorityInNavigationConfigForDocumentationPageSupportsMixingKeyedAndListConfig()
    {
        self::mockConfig(['docs.sidebar_order' => [
            'foo',
            'bar' => 10,
            'baz',
        ]]);

        $factory = new NavigationConfigTestClass($this->makeCoreDataObject('foo', pageClass: DocumentationPage::class));
        $this->assertSame(500, $factory->makePriority());

        $factory = new NavigationConfigTestClass($this->makeCoreDataObject('bar', pageClass: DocumentationPage::class));
        $this->assertSame(10, $factory->makePriority());

        $factory = new NavigationConfigTestClass($this->makeCoreDataObject('baz', pageClass: DocumentationPage::class));
        $this->assertSame(501, $factory->makePriority());

        $factory = new NavigationConfigTestClass($this->makeCoreDataObject('qux', pageClass: DocumentationPage::class));
        $this->assertSame(999, $factory->makePriority());
    }

    public function testRouteKeysCanBeUsedForDocumentationSidebarPriorities()
    {
        self::mockConfig(['docs.sidebar_order' => [
            'key/foo',
            'key/bar',
            'baz',
        ]]);

        $factory = new NavigationConfigTestClass($this->makeCoreDataObject('foo', routeKey: 'key/foo', pageClass: DocumentationPage::class));
        $this->assertSame(500, $factory->makePriority());

        $factory = new NavigationConfigTestClass($this->makeCoreDataObject('bar', routeKey: 'key/bar', pageClass: DocumentationPage::class));
        $this->assertSame(501, $factory->makePriority());

        $factory = new NavigationConfigTestClass($this->makeCoreDataObject('baz', routeKey: 'key', pageClass: DocumentationPage::class));
        $this->assertSame(502, $factory->makePriority());
    }

    public function testMakeGroupUsesFrontMatterGroupIfSet()
    {
        $frontMatter = new FrontMatter(['navigation.group' => 'Test Group']);
        $coreDataObject = new CoreDataObject($frontMatter, new Markdown(), MarkdownPage::class, 'test.md', '', '', '');
        $factory = new NavigationConfigTestClass($coreDataObject);

        $this->assertSame('Test Group', $factory->makeGroup());
    }

    public function testMakeGroupUsesFrontMatterGroupIfSetRegardlessOfSubdirectoryConfiguration()
    {
        self::mockConfig(['hyde.navigation.subdirectories' => 'hidden']);

        $frontMatter = new FrontMatter(['navigation.group' => 'Test Group']);
        $coreDataObject = new CoreDataObject($frontMatter, new Markdown(), MarkdownPage::class, 'test.md', '', '', '');
        $factory = new NavigationConfigTestClass($coreDataObject);

        $this->assertSame('Test Group', $factory->makeGroup());
    }

    public function testMakeGroupDefaultsToNullIfFrontMatterGroupNotSetAndSubdirectoriesNotUsed()
    {
        self::mockConfig(['hyde.navigation.subdirectories' => 'hidden']);

        $coreDataObject = new CoreDataObject(new FrontMatter(), new Markdown(), MarkdownPage::class, 'test.md', '', '', '');
        $factory = new NavigationConfigTestClass($coreDataObject);

        $this->assertNull($factory->makeGroup());
    }

    protected function makeCoreDataObject(string $identifier = '', string $routeKey = '', string $pageClass = MarkdownPage::class): CoreDataObject
    {
        return new CoreDataObject(new FrontMatter(), new Markdown(), $pageClass, $identifier, '', '', $routeKey);
    }
}

class NavigationConfigTestClass extends NavigationDataFactory
{
    public function __construct(CoreDataObject $pageData)
    {
        parent::__construct($pageData, '');
    }

    public function makePriority(): int
    {
        return parent::makePriority();
    }

    public function makeGroup(): ?string
    {
        return parent::makeGroup();
    }
}
