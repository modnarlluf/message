<?php
declare(strict_types=1);

namespace Avalonia\Component\Message\Exception;

/**
 * Class MapperInvalidArgumentException
 * @package Avalonia\Component\Message\Exception
 * @author Benjamin Perche <benjamin@perche.me>
 */
class MapperInvalidArgumentException extends MapperException
{
    public static function typeErrorFactory(
        string $expectedType,
        $givenValue,
        \Exception $previousException = null
    ): MapperInvalidArgumentException {
        return new static(
            sprintf(
                "The argument passed to the mapper must be a %s. %s given",
                $expectedType,
                is_object($givenValue) ? get_class($givenValue): gettype($givenValue)
            ),
            null,
            $previousException
        );
    }
}