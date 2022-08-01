<?php

namespace Hyde\Framework\Testing\Feature;

use Hyde\Framework\Models\Pages\DocumentationPage;
use Hyde\Framework\Services\MarkdownService;
use Hyde\Testing\TestCase;
use Illuminate\Support\Facades\Config;

/**
 * @covers \Hyde\Framework\Services\MarkdownService
 */
class MarkdownServiceTest extends TestCase
{
    public function test_service_can_parse_markdown_to_html()
    {
        $markdown = '# Hello World!';

        $html = (new MarkdownService($markdown))->parse();

        $this->assertIsString($html);
        $this->assertEquals("<h1>Hello World!</h1>\n", $html);
    }

    public function test_service_can_parse_markdown_to_html_with_permalinks()
    {
        $markdown = '## Hello World!';

        $html = (new MarkdownService($markdown))->withPermalinks()->parse();

        $this->assertIsString($html);
        $this->assertEquals(
            '<h2>Hello World!<a id="hello-world" href="#hello-world" class="heading-permalink" aria-hidden="true" '.
            'title="Permalink">#</a></h2>'."\n",
            $html
        );
    }

    public function test_torchlight_extension_is_not_enabled_by_default()
    {
        $markdown = '# Hello World!';
        $service = new MarkdownService($markdown);
        $service->parse();
        $this->assertNotContains('Torchlight\Commonmark\V2\TorchlightExtension', $service->getExtensions());
    }

    public function test_torchlight_extension_is_enabled_automatically_when_has_torchlight_feature()
    {
        $markdown = '# Hello World!';
        $service = new MarkdownService($markdown);
        $service->addFeature('torchlight')->parse();
        $this->assertContains('Torchlight\Commonmark\V2\TorchlightExtension', $service->getExtensions());
    }

    public function test_torchlight_integration_injects_attribution()
    {
        $markdown = '# Hello World! <!-- Syntax highlighted by torchlight.dev -->';

        // Enable the extension in config

        $service = new MarkdownService($markdown);

        $html = $service->parse();

        $this->assertStringContainsString('Syntax highlighting by <a href="https://torchlight.dev/" '
                .'rel="noopener nofollow">Torchlight.dev</a>', $html);
    }

    public function test_bladedown_is_not_enabled_by_default()
    {
        $service = new MarkdownService('[Blade]: {{ "Hello World!" }}');
        $this->assertEquals("<p>[Blade]: {{ &quot;Hello World!&quot; }}</p>\n", $service->parse());
    }

    public function test_bladedown_can_be_enabled()
    {
        config(['markdown.enable_blade' => true]);
        $service = new MarkdownService('[Blade]: {{ "Hello World!" }}');
        $service->addFeature('bladedown')->parse();
        $this->assertEquals("Hello World!\n", $service->parse());
    }

    // test raw html tags are stripped by default
    public function test_raw_html_tags_are_stripped_by_default()
    {
        $markdown = '<p>foo</p><style>bar</style><script>hat</script>';
        $service = new MarkdownService($markdown);
        $html = $service->parse();
        $this->assertEquals("<p>foo</p>&lt;style>bar&lt;/style>&lt;script>hat&lt;/script>\n", $html);
    }

    // test raw html tags are not stripped when explicitly enabled
    public function test_raw_html_tags_are_not_stripped_when_explicitly_enabled()
    {
        config(['markdown.allow_html' =>true]);
        $markdown = '<p>foo</p><style>bar</style><script>hat</script>';
        $service = new MarkdownService($markdown);
        $html = $service->parse();
        $this->assertEquals("<p>foo</p><style>bar</style><script>hat</script>\n", $html);
    }

    public function test_has_features_array()
    {
        $service = $this->makeService();

        $this->assertIsArray($service->features);
    }

    public function test_the_features_array_is_empty_by_default()
    {
        $service = $this->makeService();

        $this->assertEmpty($service->features);
    }

    public function test_features_can_be_added_to_the_array()
    {
        $service = $this->makeService();

        $service->addFeature('test');
        $this->assertContains('test', $service->features);
    }

    public function test_features_can_be_removed_from_the_array()
    {
        $service = $this->makeService();

        $service->addFeature('test');
        $service->removeFeature('test');
        $this->assertNotContains('test', $service->features);
    }

    public function test_method_chaining_can_be_used_to_programmatically_add_features_to_the_array()
    {
        $service = $this->makeService();

        $service->addFeature('test')->addFeature('test2');
        $this->assertContains('test', $service->features);
        $this->assertContains('test2', $service->features);
    }

    public function test_method_chaining_can_be_used_to_programmatically_remove_features_from_the_array()
    {
        $service = $this->makeService();

        $service->addFeature('test')->addFeature('test2')->removeFeature('test');
        $this->assertNotContains('test', $service->features);
        $this->assertContains('test2', $service->features);
    }

    public function test_method_with_table_of_contents_method_chain_adds_the_table_of_contents_feature()
    {
        $service = $this->makeService();

        $service->withTableOfContents();
        $this->assertContains('table-of-contents', $service->features);
    }

    public function test_method_with_permalinks_method_chain_adds_the_permalinks_feature()
    {
        $service = $this->makeService();

        $service->withPermalinks();
        $this->assertContains('permalinks', $service->features);
    }

    public function test_has_feature_returns_true_if_the_feature_is_in_the_array()
    {
        $service = $this->makeService();

        $service->addFeature('test');
        $this->assertTrue($service->hasFeature('test'));
    }

    public function test_has_feature_returns_false_if_the_feature_is_not_in_the_array()
    {
        $service = $this->makeService();

        $this->assertFalse($service->hasFeature('test'));
    }

    public function test_method_can_enable_permalinks_returns_true_if_the_permalinks_feature_is_in_the_array()
    {
        $service = $this->makeService();

        $service->addFeature('permalinks');
        $this->assertTrue($service->canEnablePermalinks());
    }

    public function test_method_can_enable_permalinks_is_automatically_for_documentation_pages()
    {
        $service = $this->makeService();

        Config::set('docs.table_of_contents.enabled', true);
        $service->sourceModel = DocumentationPage::class;

        $this->assertTrue($service->canEnablePermalinks());
    }

    public function test_method_can_enable_permalinks_returns_false_if_the_permalinks_feature_is_not_in_the_array()
    {
        $service = $this->makeService();

        $this->assertFalse($service->canEnablePermalinks());
    }

    public function test_method_can_enable_torchlight_returns_true_if_the_torchlight_feature_is_in_the_array()
    {
        $service = $this->makeService();

        $service->addFeature('torchlight');
        $this->assertTrue($service->canEnableTorchlight());
    }

    public function test_method_can_enable_torchlight_returns_false_if_the_torchlight_feature_is_not_in_the_array()
    {
        $service = $this->makeService();

        $this->assertFalse($service->canEnableTorchlight());
    }

    protected function makeService()
    {
        return new class extends MarkdownService
        {
            public array $features = [];

            public function __construct(string $markdown = '', ?string $sourceModel = null)
            {
                parent::__construct($markdown, $sourceModel);
            }
        };
    }
}
