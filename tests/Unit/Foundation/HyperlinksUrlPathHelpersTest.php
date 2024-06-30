<?php

declare(strict_types=1);

namespace Hyde\Framework\Testing\Unit\Foundation;

use Hyde\Foundation\HydeKernel;
use Hyde\Foundation\Kernel\Hyperlinks;
use Hyde\Framework\Exceptions\BaseUrlNotSetException;
use Hyde\Testing\TestCase;

/**
 * @covers \Hyde\Foundation\Kernel\Hyperlinks
 * @covers \Hyde\Framework\Exceptions\BaseUrlNotSetException
 */
class HyperlinksUrlPathHelpersTest extends TestCase
{
    protected Hyperlinks $class;

    protected function setUp(): void
    {
        parent::setUp();

        $this->class = new Hyperlinks(HydeKernel::getInstance());
    }

    public function testHasSiteUrlReturnsFalseWhenSiteUrlIsNotSet()
    {
        $this->withoutSiteUrl();

        $this->assertFalse($this->class->hasSiteUrl());
    }

    public function testHasSiteUrlReturnsFalseWhenNoSiteUrlIsSet()
    {
        $this->withSiteUrl(null);

        $this->assertFalse($this->class->hasSiteUrl());
    }

    public function testHasSiteUrlReturnsFalseWhenSiteUrlIsEmpty()
    {
        $this->withSiteUrl('');

        $this->assertFalse($this->class->hasSiteUrl());
    }

    public function testHasSiteUrlReturnsFalseWhenSiteUrlIsLocalhost()
    {
        $this->withSiteUrl('http://localhost');

        $this->assertFalse($this->class->hasSiteUrl());
    }

    public function testHasSiteUrlReturnsTrueWhenSiteUrlIsSet()
    {
        $this->withSiteUrl();

        $this->assertTrue($this->class->hasSiteUrl());
    }

    public function testHasSiteUrlReturnsTrueWhenSiteUrlIsSetEvenIfUrlIsInvalid()
    {
        $this->withSiteUrl('foo');

        $this->assertTrue($this->class->hasSiteUrl());
    }

    public function testQualifiedUrlReturnsSiteUrlWhenNoPathIsGiven()
    {
        $this->withSiteUrl();

        $this->assertSame('https://example.com', $this->class->url());
    }

    public function testQualifiedUrlReturnsSiteUrlPlusGivenPath()
    {
        $this->withSiteUrl();

        $this->assertSame('https://example.com/path', $this->class->url('path'));
    }

    public function testQualifiedUrlReturnsSiteUrlPlusGivenPathWithExtension()
    {
        $this->withSiteUrl();

        $this->assertSame('https://example.com/path.html', $this->class->url('path.html'));
    }

    public function testQualifiedUrlReturnsSiteUrlPlusGivenPathWithExtensionAndQueryString()
    {
        $this->withSiteUrl();

        $this->assertSame('https://example.com/path.html?query=string', $this->class->url('path.html?query=string'));
    }

    public function testQualifiedUrlTrimsTrailingSlashes()
    {
        $this->withSiteUrl('https://example.com/');

        $this->assertSame('https://example.com', $this->class->url());
        $this->assertSame('https://example.com', $this->class->url('/'));
        $this->assertSame('https://example.com/foo', $this->class->url('/foo/'));
    }

    public function testQualifiedUrlAcceptsMultipleSchemes()
    {
        $this->withSiteUrl('http://example.com');

        $this->assertSame('http://example.com', $this->class->url());
    }

    public function testQualifiedUrlHelperWithAlreadyQualifiedUrl()
    {
        $this->assertSame('https://example.com/foo', $this->class->url('https://example.com/foo'));
        $this->assertSame('http://localhost/foo', $this->class->url('http://localhost/foo'));
    }

    public function testQualifiedUrlHelperWithAlreadyQualifiedUrlWhenSiteUrlIsSet()
    {
        $this->app['config']->set(['hyde.url' => 'https://example.com']);

        $this->assertSame('https://example.com/foo', $this->class->url('https://example.com/foo'));
        $this->assertSame('http://localhost/foo', $this->class->url('http://localhost/foo'));
    }

    public function testQualifiedUrlHelperWithAlreadyQualifiedUrlWhenSiteUrlIsSetToSomethingElse()
    {
        $this->app['config']->set(['hyde.url' => 'my-site.com']);

        $this->assertSame('https://example.com/foo', $this->class->url('https://example.com/foo'));
        $this->assertSame('http://localhost/foo', $this->class->url('http://localhost/foo'));
    }

    public function testQualifiedUrlHelperWithAlreadyQualifiedUrlStillFormatsPath()
    {
        $this->assertSame('https://example.com/foo/bar.html', $this->class->url('https://example.com/foo/bar.html'));
        $this->assertSame('http://localhost/foo/bar.html', $this->class->url('http://localhost/foo/bar.html'));
        $this->assertSame('http://localhost/foo/bar', $this->class->url('http://localhost/foo/bar/'));
    }

    public function testQualifiedUrlHelperWithAlreadyQualifiedUrlStillFormatsPathWhenSiteUrlIsSet()
    {
        $this->app['config']->set(['hyde.url' => 'https://example.com']);
        $this->assertSame('https://example.com/foo/bar.html', $this->class->url('https://example.com/foo/bar.html'));
        $this->assertSame('http://localhost/foo/bar.html', $this->class->url('http://localhost/foo/bar.html'));
        $this->assertSame('http://localhost/foo/bar', $this->class->url('http://localhost/foo/bar/'));
    }

    public function testQualifiedUrlHelperWithAlreadyQualifiedUrlStillFormatsPathWithPrettyUrls()
    {
        $this->app['config']->set(['hyde.url' => 'https://example.com', 'hyde.pretty_urls' => true]);
        $this->assertSame('https://example.com/foo/bar', $this->class->url('https://example.com/foo/bar.html'));
        $this->assertSame('http://localhost/foo/bar', $this->class->url('http://localhost/foo/bar.html'));
        $this->assertSame('http://localhost/foo/bar', $this->class->url('http://localhost/foo/bar/'));
    }

    public function testQualifiedUrlThrowsExceptionWhenNoSiteUrlIsSet()
    {
        $this->withSiteUrl(null);

        $this->expectException(BaseUrlNotSetException::class);
        $this->expectExceptionMessage('No site URL has been set in config (or .env).');

        $this->class->url();
    }

    public function testHelperFallsBackToRelativeLinksWhenNoSiteUrlIsSet()
    {
        $this->withSiteUrl('');

        $this->assertSame('index.html', $this->class->url('index.html'));
        $this->assertSame('foo.html', $this->class->url('foo.html'));
        $this->assertSame('foo/bar.html', $this->class->url('foo/bar.html'));
        $this->assertSame('docs/index.html', $this->class->url('docs/index.html'));
    }

    public function testHelperFallsBackToPrettyRelativeLinksWhenNoSiteUrlIsSetAndPrettyUrlsAreEnabled()
    {
        config(['hyde.url' => '', 'hyde.pretty_urls' => true]);

        $this->assertSame('/', $this->class->url('index.html'));
        $this->assertSame('foo', $this->class->url('foo.html'));
        $this->assertSame('docs/', $this->class->url('docs/index.html'));
        $this->assertSame('foo/bar', $this->class->url('foo/bar.html'));
    }

    public function testHelperFallsBackToRelativeLinksWhenSiteUrlIsSetToLocalhost()
    {
        $this->withSiteUrl('http://localhost');

        $this->assertSame('index.html', $this->class->url('index.html'));
        $this->assertSame('foo.html', $this->class->url('foo.html'));
        $this->assertSame('foo/bar.html', $this->class->url('foo/bar.html'));
        $this->assertSame('docs/index.html', $this->class->url('docs/index.html'));
    }

    public function testHelperFallsBackToPrettyRelativeLinksWhenSiteUrlIsSetToLocalhostAndPrettyUrlsAreEnabled()
    {
        config(['hyde.url' => 'http://localhost', 'hyde.pretty_urls' => true]);

        $this->assertSame('/', $this->class->url('index.html'));
        $this->assertSame('foo', $this->class->url('foo.html'));
        $this->assertSame('docs/', $this->class->url('docs/index.html'));
        $this->assertSame('foo/bar', $this->class->url('foo/bar.html'));
    }

    public function testHelperReturnsExpectedStringWhenSiteUrlIsSet()
    {
        $this->withSiteUrl();

        $this->assertSame('https://example.com/foo/bar.html', $this->class->url('foo/bar.html'));
    }

    public function testHelperReturnsExpectedStringWhenPrettyUrlsAreEnabled()
    {
        config(['hyde.url' => 'https://example.com', 'hyde.pretty_urls' => true]);

        $this->assertSame('https://example.com', $this->class->url('index.html'));
        $this->assertSame('https://example.com/foo', $this->class->url('foo.html'));
        $this->assertSame('https://example.com/docs', $this->class->url('docs/index.html'));
    }
}
