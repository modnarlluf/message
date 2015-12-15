<?php
declare(strict_types=1);

namespace Avalonia\Component\Message;

/**
 * Interface MessageSenderInterface
 * @package Avalonia\Component\Message
 * @author Benjamin Perche <benjamin@perche.me>
 */
interface MessageSenderInterface
{
    /**
     * @param MessageInterface $message
     * @return void
     *
     * @throws Exception\SendMessageException If the message has failed to be sent
     * @throws Exception\MessageNotValidException If the message can't be sent has it is not valid
     */
    public function send(MessageInterface $message);
}