<?php

namespace Hyde\Framework\Testing\Unit;

use Hyde\Framework\Hyde;
use Hyde\Framework\Models\FrontMatter;
use Hyde\Framework\Models\Markdown;
use Hyde\Framework\Models\MarkdownDocument;
use Hyde\Testing\TestCase;

/**
 * @covers \Hyde\Framework\Models\MarkdownDocument
 * @covers \Hyde\Framework\Models\Markdown
 */
class MarkdownDocumentTest extends TestCase
{
    public function test_constructor_creates_new_markdown_document()
    {
        $document = new MarkdownDocument([], '');
        $this->assertInstanceOf(MarkdownDocument::class, $document);
    }

    public function test_constructor_arguments_are_optional()
    {
        $document = new MarkdownDocument();
        $this->assertInstanceOf(MarkdownDocument::class, $document);
    }

    public function test_constructor_arguments_are_assigned()
    {
        $document = new MarkdownDocument(['foo' => 'bar'], 'Hello, world!');
        $this->assertEquals(FrontMatter::fromArray(['foo' => 'bar']), $document->matter);
        $this->assertEquals('Hello, world!', $document->body);
    }

    public function test_magic_to_string_method_returns_body()
    {
        $document = new MarkdownDocument(['foo' => 'bar'], 'Hello, world!');
        $this->assertEquals('Hello, world!', (string) $document);
    }

    public function test_render_method_returns_rendered_html()
    {
        $document = new MarkdownDocument([], 'Hello, world!');
        $this->assertEquals("<p>Hello, world!</p>\n", $document->markdown->compile());
    }

    public function test_parse_method_parses_a_file_using_the_markdown_file_service()
    {
        file_put_contents('_pages/foo.md', "---\nfoo: bar\n---\nHello, world!");
        $document = MarkdownDocument::parseFile('_pages/foo.md');
        $this->assertInstanceOf(MarkdownDocument::class, $document);
        $this->assertEquals('Hello, world!', $document->markdown()->body());
        $this->assertEquals(FrontMatter::fromArray(['foo' => 'bar']), $document->matter());
        unlink(Hyde::path('_pages/foo.md'));
    }

    public function test_to_array_method_returns_array_markdown_body_lines()
    {
        $document = new MarkdownDocument(body: "foo\nbar\nbaz");
        $this->assertEquals(['foo', 'bar', 'baz'], $document->markdown->toArray());
    }

    public function test_from_file_method_returns_new_markdown_document()
    {
        file_put_contents('_pages/foo.md', "---\nfoo: bar\n---\nHello, world!");
        $markdown = Markdown::fromFile('_pages/foo.md');
        $this->assertInstanceOf(Markdown::class, $markdown);
        $this->assertEquals('Hello, world!', $markdown->body());
        unlink(Hyde::path('_pages/foo.md'));
    }
}
