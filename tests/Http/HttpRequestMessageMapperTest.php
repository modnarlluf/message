<?php
declare(strict_types = 1);

namespace Avalonia\Component\Message\Tests\Http;

use Avalonia\Component\Message\Http\HttpMessage;
use Avalonia\Component\Message\Http\HttpRequestMessageMapper;
use Avalonia\Component\Message\Stream\StringStream;

/**
 * Class HttpRequestMessageMapper
 * @package Avalonia\Component\Message\Tests\Http
 * @author Benjamin Perche <benjamin@perche.me>
 */
class HttpRequestMessageMapperTest extends \PHPUnit_Framework_TestCase
{
    /** @var HttpRequestMessageMapper */
    private $mapper;

    protected function setUp()
    {
        $this->mapper = new HttpRequestMessageMapper();
    }

    public function testHttp0_9GetRequestIsParsed()
    {
        $this->http0_9RequestTest("GET /foo/bar", "GET", "/foo/bar");
    }

    public function testHttp0_9PostRequestIsParsed()
    {
        // Warning: The mapper can parse an HTTP 0.9 POST request.
        // It's a mapper, its goal isn't to check if the request is valid or not
        $this->http0_9RequestTest(
            "POST /foo/bar/baz?foo=bar&baz",
            "POST",
            "/foo/bar/baz",
            ["foo" => "bar", "baz" => null]
        );
    }

    private function http0_9RequestTest($request, $method, $uri, array $query = array())
    {
        $message = new HttpMessage();
        $this->mapper->mapDataToStream(new StringStream($request), $message);

        $this->assertEquals("0.9", $message->getHttpVersion());
        $this->assertEquals($method, $message->getHttpMethod());
        $this->assertEquals($uri, $message->getHttpUri());

        foreach ($query as $name => $value) {
            $this->assertTrue($message->hasQueryHeader($name));
            $this->assertEquals($value, $message->getQueryHeader($name));
        }
    }
}
