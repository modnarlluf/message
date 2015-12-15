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
     * Return true if the current message has a defined HTTP verison
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
     * Return true if the current message has a defined HTTP verison
     */
    public function hasHttpMethod(): bool
    {
        return null !== $this->getHttpMethod();
    }
}