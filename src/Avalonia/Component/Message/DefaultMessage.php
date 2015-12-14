<?php
declare(strict_types = 1);

namespace Avalonia\Component\Message;

use Hoa\Stream\IStream\In;

/**
 * Class DefaultMessage
 * @package Avalonia\Component\Message
 * @author Benjamin Perche <benjamin@perche.me>
 */
class DefaultMessage implements MessageInterface
{
    /** @var ExchangeInterface */
    private $exchange;

    /** @var string */
    private $messageId;

    /** @var bool */
    private $fault;

    /** @var array */
    private $headers = array();

    /** @var In[] */
    private $attachments = array();

    /** @var mixed */
    private $body;

    public function __construct(ExchangeInterface $exchange = null)
    {
        $this->exchange = $exchange;
    }

    /**
     * @return ExchangeInterface
     *
     * The exchange that is currently handling this message.
     */
    public function getExchange(): ExchangeInterface
    {
        return $this->exchange;
    }

    /**
     * @param bool $fault
     * @return void
     *
     * Trigger that the message is on fault, or remove the fault.
     */
    public function setFault(bool $fault = true)
    {
        $this->fault = $fault;
    }

    /**
     * @return bool
     *
     * Return whether the message is on fault or not.
     */
    public function isFault(): bool
    {
        return $this->fault;
    }

    /**
     * @param string $name
     * @param mixed $default
     * @return mixed
     *
     * Return the header associated with $name or $default if it doesn't exist.
     */
    public function getHeader(string $name, $default = null)
    {
        if ($this->hasHeader($name)) {
            return $this->headers[$name];
        }

        return $default;
    }

    /**
     * @param string $name
     * @return bool
     *
     * Return whether the header has been set.
     */
    public function hasHeader(string $name): bool
    {
        return array_key_exists($name, $this->headers);
    }

    /**
     * @param string $name
     * @return mixed
     *
     * Removes a header. Return its previous value if it had one, and null instead.
     */
    public function removeHeader(string $name)
    {
        $returnValue = $this->getHeader($name);

        unset($this->headers[$name]);
        return $returnValue;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return mixed
     *
     * Set a header. Return its previous value if it had one, and null instead.
     */
    public function setHeader(string $name, $value)
    {
        $returnValue = $this->getHeader($name);

        $this->headers[$name] = $value;
        return $returnValue;
    }

    /**
     * @param array $headers
     * @return void
     *
     * Maps $headers into this object headers with setHeader
     */
    public function setHeaders(array $headers)
    {
        $this->headers = [];

        foreach ($headers as $name => $value) {
            $this->setHeader($name, $value);
        }
    }

    /**
     * @return array
     *
     * Return all the defined headers.
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @return void
     *
     * Removes all the headers
     */
    public function clearHeaders()
    {
        $this->headers = [];
    }

    /**
     * @return bool
     *
     * Return true if at least one header is defined
     */
    public function hasHeaders(): bool
    {
        return 0 < count($this->headers);
    }

    /**
     * @return MessageInterface
     *
     * Return a copy of itself.
     */
    public function copy(): MessageInterface
    {
        return clone $this;
    }

    /**
     * @param MessageInterface $message
     * @return void
     *
     * Copy $message contents in $this.
     */
    public function copyFromMessage(MessageInterface $message)
    {
        $this->clearAttachments();
        $this->clearHeaders();

        $this->exchange = $message->getExchange();
        $this->setMessageId($message->getMessageId());
        $this->setHeaders($message->getHeaders());
        $this->setBody($message->getBody());
        $this->setFault($message->isFault());
    }

    /**
     * @return string
     *
     * Get the current message id.
     */
    public function getMessageId(): string
    {
        return $this->messageId;
    }

    /**
     * @param string $messageId
     * @return void
     *
     * Set the current message id.
     */
    public function setMessageId(string $messageId)
    {
        $this->messageId = $messageId;
    }

    /**
     * @return string|In
     *
     * Return the message body.
     */
    public function getBody()
    {
        return $this->body;
    }

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
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * @param string $name
     * @param null|In $default
     * @return mixed
     *
     * Return the attachment associated with $name or $default if it doesn't exist.
     */
    public function getAttachment(string $name, In $default = null)
    {
        if ($this->hasAttachment($name)) {
            return $this->attachments[$name];
        }

        return $default;
    }

    /**
     * @return string[]
     *
     * Return all the attachment names
     */
    public function getAttachmentNames(): array
    {
        return array_keys($this->attachments);
    }

    /**
     * @param string $name
     * @return bool
     *
     * Return whether the attachment has been set.
     */
    public function hasAttachment(string $name): bool
    {
        return isset($this->attachments[$name]);
    }

    /**
     * @param string $name
     * @return mixed
     *
     * Removes an attachment. Return its previous value if it had one, and null instead.
     */
    public function removeAttachment(string $name)
    {
        $returnValue = $this->getAttachment($name);

        unset($this->attachments[$name]);
        return $returnValue;
    }

    /**
     * @param string $name
     * @param In $value
     * @return mixed
     *
     * Add an attachment. Return its previous value if it had one, and null instead.
     */
    public function addAttachment(string $name, In $value)
    {
        $returnValue = $this->getAttachment($name);

        $this->attachments[$name] = $value;
        return $returnValue;
    }

    /**
     * @param In[] $attachments
     * @return void
     *
     * Maps $attachments in this object attachments using addAttachment method.
     */
    public function setAttachments(array $attachments)
    {
        foreach ($attachments as $name => $attachment) {
            $this->addAttachment($name, $attachment);
        }
    }

    /**
     * @return array
     *
     * Return all the defined attachments.
     */
    public function getAttachments(): array
    {
        return $this->attachments;
    }

    /**
     * @return void
     *
     * Removes all the message attachments
     */
    public function clearAttachments()
    {
        $this->attachments = [];
    }

    /**
     * @return bool
     *
     * Return true if the message has at least one attachment
     */
    public function hasAttachments(): bool
    {
        return 0 < count($this->attachments);
    }
}
