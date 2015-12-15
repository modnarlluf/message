<?php
declare(strict_types=1);

namespace Avalonia\Component\Message;

use Hoa\Stream\IStream\In;

/**
 * Interface MessageInterface
 * @package Avalonia\Component\Message
 * @author Benjamin Perche <benjamin@perche.me>
 */
interface MessageInterface
{
    /**
     * @return ExchangeInterface
     *
     * The exchange that is currently handling this message.
     */
    public function getExchange(): ExchangeInterface;

    /**
     * @param bool $fault
     * @return void
     *
     * Trigger that the message is on fault, or remove the fault.
     */
    public function setFault(bool $fault = true);

    /**
     * @return bool
     *
     * Return whether the message is on fault or not.
     */
    public function isFault(): bool ;

    /**
     * @param string $name
     * @param mixed $default
     * @return mixed
     *
     * Return the header associated with $name or $default if it doesn't exist.
     */
    public function getHeader(string $name, $default = null);

    /**
     * @param string $name
     * @return bool
     *
     * Return whether the header has been set.
     */
    public function hasHeader(string $name): bool;

    /**
     * @param string $name
     * @return mixed
     *
     * Removes a header. Return its previous value if it had one, and null instead.
     */
    public function removeHeader(string $name);

    /**
     * @param string $name
     * @param mixed $value
     * @return mixed
     *
     * Set a header. Return its previous value if it had one, and null instead.
     */
    public function setHeader(string $name, $value);

    /**
     * @param array $headers
     * @return void
     *
     * Maps $headers into this object headers with setHeader
     */
    public function setHeaders(array $headers);

    /**
     * @return array
     *
     * Return all the defined headers.
     */
    public function getHeaders(): array;

    /**
     * @return void
     *
     * Removes all the headers
     */
    public function clearHeaders();

    /**
     * @return bool
     *
     * Return true if at least one header is defined
     */
    public function hasHeaders(): bool;

    /**
     * @return MessageInterface
     *
     * Return a copy of itself.
     */
    public function copy(): MessageInterface;

    /**
     * @param MessageInterface $message
     * @return void
     *
     * Copy $message contents in $this.
     */
    public function copyFromMessage(MessageInterface $message);

    /**
     * @return string
     *
     * Get the current message id.
     */
    public function getMessageId(): string;

    /**
     * @param string $messageId
     * @return void
     *
     * Set the current message id.
     */
    public function setMessageId(string $messageId);

    /**
     * @return string|In
     *
     * Return the message body.
     */
    public function getBody();

    /**
     * @param string|In $body
     * @return void
     *
     * The message body can be anything describing a body.
     * The best form would be an \Hoa\Stream\IStream\In, but it can also be a string.
     *
     * If the body isn't a known type, it will be casted as string if it implements __toString.
     * If it doesn't, an exception will be thrown.
     */
    public function setBody($body);

    /**
     * @param string $name
     * @param null|In $default
     * @return mixed
     *
     * Return the attachment associated with $name or $default if it doesn't exist.
     */
    public function getAttachment(string $name, In $default = null);

    /**
     * @return string[]
     *
     * Return all the attachment names
     */
    public function getAttachmentNames(): array;

    /**
     * @param string $name
     * @return bool
     *
     * Return whether the attachment has been set.
     */
    public function hasAttachment(string $name): bool;

    /**
     * @param string $name
     * @return mixed
     *
     * Removes an attachment. Return its previous value if it had one, and null instead.
     */
    public function removeAttachment(string $name);

    /**
     * @param string $name
     * @param In $value
     * @return mixed
     *
     * Add an attachment. Return its previous value if it had one, and null instead.
     */
    public function addAttachment(string $name, In $value);

    /**
     * @param In[] $attachments
     * @return void
     *
     * Maps $attachments in this object attachments using addAttachment method.
     */
    public function setAttachments(array $attachments);

    /**
     * @return array
     *
     * Return all the defined attachments.
     */
    public function getAttachments(): array;

    /**
     * @return void
     *
     * Removes all the message attachments
     */
    public function clearAttachments();

    /**
     * @return bool
     *
     * Return true if the message has at least one attachment
     */
    public function hasAttachments(): bool;
}
