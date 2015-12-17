<?php
declare(strict_types=1);

namespace Avalonia\Component\Message;

use Hoa\Stream\IStream\{In, Out};

/**
 * Interface MessageMapperInterface
 * @package Avalonia\Component\Message
 * @author Benjamin Perche <benjamin@perche.me>
 */
interface MessageMapperInterface
{
    /**
     * @param In $inputStream The message input stream
     * @param MessageInterface $message The message to map the data into
     *
     * @throws \Avalonia\Component\Message\Exception\MapperInvalidArgumentException if the given $rawData is not of the good type
     * @throws \Avalonia\Component\Message\Exception\MapperInvalidDataException if the given $rawData doesn't not fetch the required format
     *
     * Maps the given stream into a message
     */
    public function mapDataToStream(In $inputStream, MessageInterface $message);

    /**
     * @param Out $outputStream The stream where the formatted message has to be written
     * @param MessageInterface $message
     *
     * @throws \Avalonia\Component\Message\Exception\MapperInvalidArgumentException if the given $message is not of the good type
     * @throws \Avalonia\Component\Message\Exception\MapperInvalidDataException if the given $rawData doesn't not contain required headers/attachments/body
     *
     * Writes a formatted message into the output stream
     */
    public function mapMessageToStream(Out $outputStream, MessageInterface $message);
}