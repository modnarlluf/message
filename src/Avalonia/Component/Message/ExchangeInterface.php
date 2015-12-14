<?php
declare(strict_types = 1);

namespace Avalonia\Component\Message;

use Avalonia\Core\KernelInterface;

/**
 * Interface ExchangeInterface
 * @package Avalonia\Component\Message
 * @author Benjamin Perche <benjamin@perche.me>
 */
interface ExchangeInterface
{
    /**
     * @param string $name
     * @param mixed $default
     * @return mixed
     *
     * Return the property associated with $name or $default if it doesn't exist.
     */
    public function getProperty(string $name, $default = null);

    /**
     * @param string $name
     * @return bool
     *
     * Return whether the property has been set.
     */
    public function hasProperty(string $name): bool;

    /**
     * @param string $name
     * @return mixed
     *
     * Removes a property. Return its previous value if it had one, and null instead.
     */
    public function removeProperty(string $name);

    /**
     * @param string $name
     * @param mixed $value
     * @return mixed
     *
     * Set a property. Return its previous value if it had one, and null instead.
     */
    public function setProperty(string $name, $value);

    /**
     * @return array
     *
     * Return all the defined properties.
     */
    public function getProperties(): array;

    /**
     * @return MessageInterface
     *
     * Return the current inbound message.
     */
    public function getIn(): MessageInterface;

    /**
     * @param MessageInterface $message
     * @return void
     *
     * Set the current inbound message.
     */
    public function setIn(MessageInterface $message);

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
    public function getOut(): MessageInterface;

    /**
     * @param MessageInterface $message
     * @return void
     *
     * Set the current outbound message.
     */
    public function setOut(MessageInterface $message);

    /**
     * @return bool
     *
     * Return whether the exchange has an outbound message.
     */
    public function hasOut(): bool;

    /**
     * @return KernelInterface
     */
    public function getKernel(): KernelInterface;

    /**
     * @return ExchangeInterface
     *
     * Creates a copy of the current message exchange so that it can be
     * forwarded to another destination.
     */
    public function copy(): ExchangeInterface;

    /**
     * @param \Throwable $exception
     * @return void
     *
     * Set the current exception in exchange process.
     * After being called, the ExchangeInterface::isFailed() method will return true.
     */
    public function setException(\Throwable $exception);

    /**
     * @return bool
     *
     * Return true if the ExchangeInterface::setException() is called, or if one of in/out MessageInterface::isFault(true)
     * is true.
     */
    public function isFailed(): bool;

    /**
     * @return string
     *
     * Return the route id which created the message. (unique)
     */
    public function getFromRouteId(): string;

    /**
     * @param string $fromRouteId
     * @return void
     *
     * Set the route id that created the message. (unique)
     */
    public function setFromRouteId(string $fromRouteId);

    /**
     * @return string
     *
     * Get the exchange id.
     */
    public function getExchangeId(): string;

    /**
     * @param string $exchangeId
     * @return void
     *
     * Set the exchange id.
     */
    public function setExchangeId(string $exchangeId);
}
