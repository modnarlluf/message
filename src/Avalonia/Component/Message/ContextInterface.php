<?php
declare(strict_types = 1);

namespace Avalonia\Component\Message;

use Interop\Container\ContainerInterface;

/**
 * Interface ContextInterface
 * @package Avalonia\Component\Message
 * @author Benjamin Perche <benjamin@perche.me>
 */
interface ContextInterface
{
    public function getContainer(): ContainerInterface;
}
