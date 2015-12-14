<?php
declare(strict_types = 1);

namespace Avalonia\Component\Message;

/**
 * Class DefaultExchange
 * @package Avalonia\Component\Message
 * @author Benjamin Perche <benjamin@perche.me>
 */
class DefaultExchange implements ExchangeInterface
{
    /** @var array */
    private $properties = array();

    /** @var \Throwable */
    private $exception;

    /** @var string */
    private $fromRouteId;

    /** @var string */
    private $exchangeId;

    /** @var MessageInterface */
    private $in;

    /** @var null|MessageInterface */
    private $out;

    /** @var ContextInterface */
    private $context;

    /**
     * DefaultExchange constructor.
     * @param ContextInterface $context
     * @param MessageInterface $in
     */
    public function __construct(ContextInterface $context, MessageInterface $in)
    {
        $this->context = $context;
        $this->in = $in;
    }

    public function __clone()
    {
        $this->in = $this->in->copy();

        if ($this->hasOut()) {
            $this->out = $this->out->copy();
        }
    }

    /**
     * @param string $name
     * @param mixed $default
     * @return mixed
     *
     * Return the the property associated with $name or $default if it doesn't exist.
     */
    public function getProperty(string $name, $default = null)
    {
        if ($this->hasProperty($name)) {
            return $this->properties[$name];
        }

        return $default;
    }

    /**
     * @param string $name
     * @return bool
     *
     * Return whether the property has been set.
     */
    public function hasProperty(string $name): bool
    {
        return array_key_exists($name, $this->properties);
    }

    /**
     * @param string $name
     * @return mixed
     *
     * Removes a property. Return its previous value if it had one, and null instead.
     */
    public function removeProperty(string $name)
    {
        $returnValue = $this->getProperty($name);

        unset($this->properties[$name]);
        return $returnValue;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return mixed
     *
     * Set a property. Return its previous value if it had one, and null instead.
     */
    public function setProperty(string $name, $value)
    {
        $returnValue = $this->getProperty($name);

        $this->properties[$name] = $value;
        return $returnValue;
    }

    /**
     * @return MessageInterface
     *
     * Return the current inbound message.
     */
    public function getIn(): MessageInterface
    {
        return $this->in;
    }

    /**
     * @param MessageInterface $message
     * @return void
     *
     * Set the current inbound message.
     */
    public function setIn(MessageInterface $message)
    {
        $this->in = $message;
    }

    /**
     * @return MessageInterface
     *
     * Return the current outbound message. Lazy create one if its value isn't set by copying inbound message using
     * MessageInterface::copy method.
     *
     * Important when used with routing: If you want to change the current message, then use {@link #getIn()} instead as it will
     * ensure headers etc. is kept and propagated when routing continues. Bottom line end users should rarely use
     * this method.
     *
     * If you want to test whether an OUT message have been set or not, use the ExchangeInterface::hasOut() method.
     */
    public function getOut(): MessageInterface
    {
        return $this->out ?: $this->in->copy();
    }

    /**
     * @param MessageInterface $message
     * @return void
     *
     * Set the current outbound message
     */
    public function setOut(MessageInterface $message)
    {
        $this->out = $message;
    }

    /**
     * @return bool
     *
     * Return whether the exchange has an outbound message.
     */
    public function hasOut(): bool
    {
        return null !== $this->out;
    }

    /**
     * Returns the context so that a processor can resolve endpoints from URIs
     *
     * @return ContextInterface
     */
    public function getContext(): ContextInterface
    {
        return $this->context;
    }

    /**
     * Creates a copy of the current message exchange so that it can be
     * forwarded to another destination
     */
    public function copy(): ExchangeInterface
    {
        return clone $this;
    }

    /**
     * @param \Throwable $exception
     * @return void
     *
     * Set the current exception in exchange process.
     * After being called, the ExchangeInterface::isFailed() method will return true
     */
    public function setException(\Throwable $exception)
    {
        $this->exception = $exception;
    }

    /**
     * @return bool
     *
     * Return true if the ExchangeInterface::setException() is called, or if one of in/out MessageInterface::isFault()
     * is true.
     */
    public function isFailed(): bool
    {
        return null !== $this->exception || $this->in->isFault() || ($this->hasOut() && $this->out->isFault());
    }

    /**
     * @return string
     *
     * Return the route id which created the message. (unique)
     */
    public function getFromRouteId(): string
    {
        return $this->fromRouteId;
    }

    /**
     * @param string $fromRouteId
     * @return void
     *
     * Set the route id that created the message. (unique)
     */
    public function setFromRouteId(string $fromRouteId)
    {
        $this->fromRouteId = $fromRouteId;
    }

    /**
     * @return string
     *
     * Get the exchange id
     */
    public function getExchangeId(): string
    {
        $this->exchangeId;
    }

    /**
     * @param string $exchangeId
     * @return void
     *
     * Set the exchange id
     */
    public function setExchangeId(string $exchangeId)
    {
        $this->exchangeId = $exchangeId;
    }
}
