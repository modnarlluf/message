<?php
declare(strict_types = 1);

namespace Avalonia\Component\Message\Exception;

/**
 * Class MapperInvalidDataEofFoundException
 * @package Avalonia\Component\Message\Exception
 * @author Benjamin Perche <benjamin@perche.me>
 */
class MapperInvalidDataEofFoundException extends MapperInvalidDataException
{
    private $remainingBufferData;

    /**
     * @return mixed
     */
    public function getRemainingBufferData(): string
    {
        return $this->remainingBufferData;
    }

    /**
     * @param string $remainingBufferData
     * @return MapperInvalidDataEofFoundException
     */
    public function setRemainingBufferData(string $remainingBufferData): MapperInvalidDataEofFoundException
    {
        $this->remainingBufferData = $remainingBufferData;
        return $this;
    }
}
