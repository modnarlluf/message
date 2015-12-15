<?php
declare(strict_types=1);

namespace Avalonia\Component\Message\Http;

use Avalonia\Component\Message\Exception\MapperInvalidDataException;
use Avalonia\Component\Message\MessageInterface;
use Hoa\Stream\IStream\{In, Out};

/**
 * Class HttpRequestMessageMapper
 * @package Avalonia\Component\Message\Http
 * @author Benjamin Perche <benjamin@perche.me>
 */
class HttpRequestMessageMapper extends AbstractHttpMessageMapper
{
    const HTTP_REQUEST_PATTERN = "/(?<method>[a-z]+)\s+(<path>[^\s]+)(\s+HTTP/(<verison>1.0|1.1))*/i";

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
        $firstLine = $this->readHttpLine($inputStream);

        if (0 === preg_match(static::HTTP_REQUEST_PATTERN, $firstLine, $firstLineTable)) {
            throw new MapperInvalidDataException;
        }

        // @todo
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
        // @todo
    }
}