<?php

declare(strict_types=1);

namespace Hyde\Framework\Testing\Unit;

use Hyde\Facades\Filesystem;
use Hyde\Markdown\Models\FrontMatter;
use Hyde\Markdown\Models\Markdown;
use Hyde\Markdown\Models\MarkdownDocument;
use Hyde\Testing\TestCase;
use Illuminate\Support\HtmlString;

/**
 * @covers \Hyde\Markdown\Models\MarkdownDocument
 * @covers \Hyde\Markdown\Models\Markdown
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
    }

    public function test_magic_to_string_method_returns_body()
    {
        $document = new MarkdownDocument(['foo' => 'bar'], 'Hello, world!');
        $this->assertEquals('Hello, world!', (string) $document);
    }

    public function test_compile_method_returns_rendered_html()
    {
        $document = new MarkdownDocument([], 'Hello, world!');
        $this->assertEquals("<p>Hello, world!</p>\n", $document->markdown->compile());
    }

    public function test_to_html_method_returns_rendered_as_html_string()
    {
        $document = new MarkdownDocument([], 'Hello, world!');
        $this->assertInstanceOf(HtmlString::class, $document->markdown->toHtml());
        $this->assertEquals("<p>Hello, world!</p>\n", (string) $document->markdown->toHtml());
    }

    public function test_parse_method_parses_a_file_using_the_markdown_file_service()
    {
        file_put_contents('_pages/foo.md', "---\nfoo: bar\n---\nHello, world!");
        $document = MarkdownDocument::parse('_pages/foo.md');
        $this->assertInstanceOf(MarkdownDocument::class, $document);
        $this->assertEquals('Hello, world!', $document->markdown()->body());
        $this->assertEquals(FrontMatter::fromArray(['foo' => 'bar']), $document->matter());
        Filesystem::unlink('_pages/foo.md');
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
        Filesystem::unlink('_pages/foo.md');
    }

    public function end_of_markdown_body_is_trimmed()
    {
        $markdown = new Markdown("Hello, world!\n\r\t   ");
        $this->assertEquals('Hello, world!', $markdown->body());
    }

    public function test_carriage_returns_are_normalized()
    {
        $markdown = new Markdown("foo\rbar");
        $this->assertEquals("foo\rbar", $markdown->body());

        $markdown = new Markdown("foo\r\nbar");
        $this->assertEquals("foo\nbar", $markdown->body());

        $markdown = new Markdown("foo\nbar");
        $this->assertEquals("foo\nbar", $markdown->body());
    }
}
