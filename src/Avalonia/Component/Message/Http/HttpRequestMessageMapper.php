<?php
declare(strict_types=1);

namespace Avalonia\Component\Message\Http;

use Avalonia\Component\Message\Exception\MapperInvalidDataEofFoundException;
use Avalonia\Component\Message\Http\Exception\{HttpMapperInvalidCommandException, HttpMapperInvalidHeaderException};
use Avalonia\Component\Message\MessageInterface;
use Hoa\Stream\IStream\{In, Out};

/**
 * Class HttpRequestMessageMapper
 * @package Avalonia\Component\Message\Http
 * @author Benjamin Perche <benjamin@perche.me>
 */
class HttpRequestMessageMapper extends AbstractHttpMessageMapper
{
    const DEFAULT_HTTP_VERSION = "0.9";
    const HTTP_REQUEST_COMMAND_PATTERN = "/^(?<method>[a-z]+)\s+(?<uri>[^\s\?]+)(\?(?<query>[^\s]+))?(\s+HTTP\/(?<version>1\.0|1\.1))*$/i";
    const HTTP_HEADER_NAME_PATTERN = "/^[a-z\-]+$/";
    const HTTP_HEADER_PATTERN = "/^(?<header>[a-z\-]+):\s*(?<content>.+)$/i";

    /**
     * @param In $inputStream The message input stream
     * @param MessageInterface $message The message to map the data into
     *
     * @throws \Avalonia\Component\Message\Exception\MapperInvalidArgumentException if the given $rawData is not of the good type
     * @throws \Avalonia\Component\Message\Exception\MapperInvalidDataException if the given $rawData doesn't not fetch the required format
     *
     * Maps the given stream into a message
     */
    public function mapDataToStream(In $inputStream, MessageInterface $message)
    {
        // parse http command line
        try {
            $command = $this->readHttpLine($inputStream, false, $offset);
        } catch (MapperInvalidDataEofFoundException $e) {
            // One line request, it can be an HTTP 0.9 one
            $command = $e->getRemainingBufferData();
        }

        if (0 === preg_match(static::HTTP_REQUEST_COMMAND_PATTERN, $command, $parsedCommand)) {
            throw new HttpMapperInvalidCommandException(sprintf(
                "The HTTP command line is not valid: \"%s. Expected format: COMMAND URL [HTTP/VERSION].",
                $command
            ));
        }

        $message->setHeader(HttpMessage::HEADER_HTTP_METHOD, $parsedCommand["method"]);
        $message->setHeader(HttpMessage::HEADER_HTTP_VERSION, $parsedCommand["version"] ?? static::DEFAULT_HTTP_VERSION);
        $message->setHeader(HttpMessage::HEADER_HTTP_URI, $parsedCommand["uri"]);

        // If the uri contains a query string, parse it and place its values in the headers
        if (isset($parsedCommand["query"])) {
            parse_str($parsedCommand["query"], $parsedQuery);

            foreach ($parsedQuery as $name => $value) {
                // This is equivalent to HttpMessage::setQueryHeader($name, $value)
                $message->setHeader(HttpMessage::HTTP_QUERY_HEADER_PREFIX.$name, $value);
            }
        }

        // Then read headers
        while (!$inputStream->eof()) {
            try {
                $line = $this->readHttpLine($inputStream, false, $offset);
            } catch (MapperInvalidDataEofFoundException $e) {
                // If this exception is caught, it means that the message doesn't have a body.
                // So parse the remaining data in the buffer
                $line = $e->getRemainingBufferData();
            }


            if ('' === $line) {
                // We reached the body
                $message->setBody($inputStream->readAll($offset));
                // stop the loop as the mapping is finished
                break;
            } elseif (0 === preg_match(static::HTTP_HEADER_PATTERN, $line, $parsedHeader)) {
                throw new HttpMapperInvalidHeaderException(sprintf(
                    "The given HTTP header line is not valid: \"%s\". Expected format: HEADER-NAME: VALUE.",
                    $line
                ));
            }

            $message->setHeader($parsedHeader["header"], $parsedHeader["value"]);
        }
    }

    /**
     * @param Out $outputStream The stream where the formatted message has to be written
     * @param MessageInterface $message
     *
     * @throws \Avalonia\Component\Message\Exception\MapperInvalidArgumentException if the given $message is not of the good type
     * @throws \Avalonia\Component\Message\Exception\MapperInvalidDataException if the given $rawData doesn't not contain required headers/attachments/body
     *
     * Writes a formatted message into the output stream
     */
    public function mapMessageToStream(Out $outputStream, MessageInterface $message)
    {
        // Write command line
        $commandLine = sprintf(
            "%s %s",
            $message->getHeader(HttpMessage::HEADER_HTTP_METHOD),
            $message->getHeader(HttpMessage::HEADER_HTTP_URI)
        );

        $messageHttpVersion = $message->getHeader(HttpMessage::HEADER_HTTP_VERSION);

        if (null !== $messageHttpVersion && static::DEFAULT_HTTP_VERSION !== $messageHttpVersion) {
            $commandLine .= " HTTP/".$messageHttpVersion;
        }

        $this->writeHttpLine($outputStream, $commandLine);

        foreach ($message->getHeadersByNameFormat(static::HTTP_HEADER_NAME_PATTERN) as $name => $header) {
            $this->writeHttpLine($outputStream, sprintf("%s: %s", $name, $header));
        }

        $body = $message->getBodyAsString();

        if ('' !== $body) {
            // Write the empty line between headers and the body
            $this->writeHttpLine($outputStream, '');
            $outputStream->writeString($body);
        }
    }
}