<?php
declare(strict_types = 1);

namespace Avalonia\Component\Message;

/**
 * Interface MessageInterface
 * @package Avalonia\Component\Message
 * @author Benjamin Perche <benjamin@perche.me>
 */
interface MessageInterface
{
    /**
     * @return MessageInterface
     *
     * Return a copy of itself.
     */
    public function copy(): MessageInterface;
}
