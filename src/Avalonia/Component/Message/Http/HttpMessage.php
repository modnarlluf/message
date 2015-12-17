<?php
declare(strict_types=1);

namespace Avalonia\Component\Message\Http;

use Avalonia\Component\Message\DefaultMessage;

/**
 * Class HttpMessage
 * @package Avalonia\Component\Message\Http
 * @author Benjamin Perche <benjamin@perche.me>
 */
class HttpMessage extends DefaultMessage
{
    const HEADER_HTTP_VERSION = "http.version";
    const HEADER_HTTP_METHOD = "http.method";
    const HEADER_HTTP_URI = "http.uri";

    const HTTP_VERSION_1_0 = "1.0";
    const HTTP_VERSION_1_1 = "1.0";

    const HTTP_QUERY_HEADER_PREFIX = "query.";

    /**
     * @param string $version
     * @return void
     *
     * Set the current message HTTP version
     */
    public function setHttpVersion(string $version)
    {
        $this->setHeader(static::HEADER_HTTP_VERSION, $version);
    }

    /**
     * @return string
     *
     * Get the current message HTTP version
     */
    public function getHttpVersion(): string
    {
        return $this->getHeader(static::HEADER_HTTP_VERSION);
    }

    /**
     * @return bool
     *
     * Return true if the current message has a defined HTTP version
     */
    public function hasHttpVersion(): bool
    {
        return null !== $this->getHttpVersion();
    }

    /**
     * @param string $version
     * @return void
     *
     * Set the current message HTTP method
     */
    public function setHttpMethod(string $method)
    {
        $this->setHeader(static::HEADER_HTTP_METHOD, strtoupper($method));
    }

    /**
     * @return string
     *
     * Get the current message HTTP method
     */
    public function getHttpMethod(): string
    {
        return $this->getHeader(static::HEADER_HTTP_METHOD);
    }


    /**
     * @return bool
     *
     * Return true if the current message has a defined HTTP method
     */
    public function hasHttpMethod(): bool
    {
        return null !== $this->getHttpMethod();
    }

    /**
     * @param string $version
     * @return void
     *
     * Set the current message HTTP uri
     */
    public function setHttpUri(string $uri)
    {
        $this->setHeader(static::HEADER_HTTP_URI, strtoupper($uri));
    }

    /**
     * @return string
     *
     * Get the current message HTTP uri
     */
    public function getHttpUri(): string
    {
        return $this->getHeader(static::HEADER_HTTP_URI);
    }


    /**
     * @return bool
     *
     * Return true if the current message has a defined HTTP uri
     */
    public function hasHttpUri(): bool
    {
        return null !== $this->getHttpUri();
    }

    /**
     * @param string $name
     * @param string|array|null $value
     * @return mixed
     */
    public function setQueryHeader(string $name, $value)
    {
        return $this->setHeader(static::HTTP_QUERY_HEADER_PREFIX.$name, $value);
    }

    /**
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function getQueryHeader(string $name, $default = null)
    {
        return $this->getHeader(static::HTTP_QUERY_HEADER_PREFIX.$name, $default);
    }

    public function hasQueryHeader(string $name): bool
    {
        return $this->hasHeader(static::HTTP_QUERY_HEADER_PREFIX.$name);
    }

    /**
     * @return \Generator
     */
    public function getQueryHeaders(): \Generator
    {
        $pattern = "/^".str_replace(".", "\.", static::HTTP_QUERY_HEADER_PREFIX)."/";
        return $this->getHeadersByNameFormat($pattern);
    }

    public function hasQueryHeaders(): bool
    {
        return 0 !== iterator_count($this->getHeadersByNameFormat(static::HTTP_QUERY_HEADER_PREFIX));
    }
}