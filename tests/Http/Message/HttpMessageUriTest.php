<?php
use Fyuze\Http\Message\Uri;
use PHPUnit\Framework\TestCase;

class HttpMessageUriTest extends TestCase
{
    /**
     * @var Uri
     */
    protected $uri;

    public function setUp()
    {
        $this->uri = new Uri();
    }

    public function testParsesFullUriFromConstructor()
    {
        $uri = new Uri('http://user:pass@fyuze.io:81/index.php?foo=bar#baz');

        $this->assertEquals('http', $uri->getScheme());
        $this->assertEquals('user:pass@fyuze.io:81', $uri->getAuthority());
        $this->assertEquals('user:pass', $uri->getUserInfo());
        $this->assertEquals('fyuze.io', $uri->getHost());
        $this->assertEquals(81, $uri->getPort());
        $this->assertEquals('/index.php', $uri->getPath());
        $this->assertEquals('foo=bar', $uri->getQuery());
        $this->assertEquals('baz', $uri->getFragment());
    }

    public function testBuidsFullUriFluently()
    {
        $uri = $this->uri->withScheme('http')
            ->withHost('fyuze.io')
            ->withUserInfo('user', 'pass')
            ->withPath('/index.php')
            ->withQuery('foo=bar&bar=100%')
            ->withFragment('foobar');

        $this->assertEquals('http', $uri->getScheme());
        $this->assertEquals('fyuze.io', $uri->getHost());
        $this->assertEquals('user:pass', $uri->getUserInfo());
        $this->assertEquals('/index.php', $uri->getPath());
        $this->assertEquals('foo=bar&bar=100%25', $uri->getQuery());
        $this->assertEquals('foobar', $uri->getFragment());
        $this->assertNull($uri->getPort());
    }

    public function testBuildsWithRelativeUri()
    {
        $uri = new Uri('/index.php/foo/bar?foo=bar&bar=baz#foobar');

        $this->assertEquals('/index.php/foo/bar', $uri->getPath());
        $this->assertEquals('foo=bar&bar=baz', $uri->getQuery());
        $this->assertEquals('foobar', $uri->getFragment());
        $this->assertNull($uri->getPort());
    }

    public function testWithScheme()
    {
        $uri = (new Uri('foo'));

        $this->assertEmpty($uri->getScheme());
        $this->assertEquals('http', $uri->withScheme('http')->withScheme('http')->getScheme());
        $this->assertEquals('https', $uri->withScheme('https')->getScheme());
    }

    public function testSchemeRemovesColonAndSlahses()
    {
        $uri = $this->uri->withScheme('http://');
        $this->assertEquals('http', $uri->getScheme());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testThrowsExceptionForInvalidSchemes()
    {
        $uri = $this->uri->withScheme('foo');
    }

    public function testUserInfoDoesntKeepTrailingColonWithoutPass()
    {
        $uri = $this->uri->withUserInfo('foo');
        $this->assertEquals('foo', $uri->getUserInfo());
    }

    public function testValidFormatWhenPasswordSupplied()
    {
        $uri = $this->uri->withUserInfo('foo', 'bar');
        $this->assertEquals('foo:bar', $uri->getUserInfo());
    }

    public function testStandardPortsReturnNull()
    {
        $uri = $this->uri->withScheme('http')->withPort(80);
        $this->assertNull($uri->getPort());
    }

    public function testNonStandardPortGivesPort()
    {
        $uri = $this->uri->withPort(123);
        $this->assertEquals(123, $uri->getPort());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testOutofBoundsPortsThrowsException()
    {
        $this->uri->withPort(800000);
    }

    public function testPortDetachesWithNullProvided()
    {
        $uri = $this->uri->withPort(8080);
        $this->assertEquals(8080, $uri->getPort());
        $uri = $uri->withPort(null);
        $this->assertNull($uri->getPort());
    }

    public function testPathQueryAndFragmentAreEncodedIfNecessary()
    {
        $this->assertEquals('/foo%20bar', $this->uri->withPath('foo bar')->getPath());
        $this->assertEquals('foo%20bar', $this->uri->withQuery('foo bar')->getQuery());
        $this->assertEquals('foo%20bar', $this->uri->withFragment('foo bar')->getFragment());
    }

    public function testPathQueryAndFragmentAreNotDoubleEncoded()
    {
        $this->assertEquals('/foo%20bar', $this->uri->withPath('foo%20bar')->getPath());
        $this->assertEquals('foo%20bar', $this->uri->withQuery('foo%20bar')->getQuery());
        $this->assertEquals('foo%20bar', $this->uri->withFragment('foo%20bar')->getFragment());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testExceptionThrownOnPathWithInvalidType()
    {
        $this->uri->withPath(123);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testExceptionThrownWithFragmentInPath()
    {
        $this->uri->withPath('foo#bar');
    }

    public function testFragmentRemovesLeadingHashtag()
    {
        $this->assertEquals('foobar', $this->uri->withFragment('#foobar')->getFragment());
    }

    public function testFragmentIsEncodedIfNecessary()
    {
        $this->assertEquals('foo%20bar', $this->uri->withFragment('#foo bar')->getFragment());
    }

    public function testToStringBuildsCorrectUrisFromString()
    {
        $uri = new Uri('http://foobar:81#foobar');
        $this->assertEquals('http://foobar:81#foobar', (string)$uri);
    }

    public function testToStringBuildsFullUris()
    {
        $uri = (new Uri())
            ->withScheme('https')
            ->withHost('fyuze.local')
            ->withUserInfo('user', 'pass')
            ->withPath('foo')
            ->withPort(442)
            ->withQuery('bar=baz')
            ->withFragment('qaz');

        $this->assertEquals('https://user:pass@fyuze.local:442/foo?bar=baz#qaz', (string)$uri);
    }

    public function testToStringBuildsShortUris()
    {
        $uri = (new Uri())
            ->withHost('fyuze.local')
            ->withPath('foo')
            ->withQuery('bar=baz')
            ->withFragment('qaz');

        $this->assertEquals('fyuze.local/foo?bar=baz#qaz', (string)$uri);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testThrowsExceptionWhenUnableToParseUrl()
    {
        new Uri('http:///fyuze.io');
    }
}
