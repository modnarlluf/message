<?php
declare(strict_types=1);

namespace Avalonia\Component\Message;

/**
 * Interface MessageMapperInterface
 * @package Avalonia\Component\Message
 * @author Benjamin Perche <benjamin@perche.me>
 */
interface MessageMapperInterface
{
    /**
     * @param mixed $rawData
     * @return MessageInterface
     *
     * @throws Exception\MapperInvalidArgumentException if the given $rawData is not of the good type
     * @throws Exception\MapperInvalidDataException if the given $rawData doesn't not fetch the required format
     *
     * Maps the given data into a message
     */
    public function mapDataToMessage($rawData): MessageInterface;

    /**
     * @param MessageInterface $message
     * @return mixed The formatted message
     *
     * @throws Exception\MapperInvalidArgumentException if the given $message is not of the good type
     * @throws Exception\MapperInvalidDataException if the given $rawData doesn't not contain required headers/attachments/body
     */
    public function mapMessageToData(MessageInterface $message);
}