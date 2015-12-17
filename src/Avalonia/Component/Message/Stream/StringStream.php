<?php
declare(strict_types = 1);

namespace Avalonia\Component\Message\Stream;

use Hoa\Stream\IStream\In;
use Hoa\Stream\IStream\Out;

/**
 * Class StringStream
 * @package Avalonia\Component\Message\Stream
 * @author Benjamin Perche <benjamin@perche.me>
 *
 * The class wraps a string into an IO stream.
 * Moreover, it has seek(), rewind() and end() methods to move the cursor into the buffer to be manipulated like a file handler.
 */
class StringStream implements In, Out
{
    const SEEK_ABSOLUTE = 0;
    const SEEK_RELATIVE = 1;

    /** @var string */
    private $buffer;

    /** @var int */
    private $bufferLength;

    /** @var int */
    private $cursor = 0;

    public function __construct(string $buffer = '')
    {
        $this->buffer = $buffer;
        $this->bufferLength = strlen($buffer);
    }

    /**
     * Write n characters.
     *
     * @param   string $string String.
     * @param   int $length Length.
     * @return  mixed
     */
    public function write($string, $length)
    {
        $suffix = substr($this->buffer, $this->cursor, $this->bufferLength-1);
        $this->buffer = substr($this->buffer, 0, $this->cursor);

        for ($i = 0; $i < $length; ++$i) {
            $this->buffer .= $string[$i] ?? "\0";
        }

        $this->bufferLength += $length;
    }

    /**
     * Write a string.
     *
     * @param   string $string String.
     * @return  mixed
     */
    public function writeString($string)
    {
        $this->write($string, strlen($string));
    }

    /**
     * Write a character.
     *
     * @param   string $character Character.
     * @return  mixed
     */
    public function writeCharacter($character)
    {
        $this->write($character, 1);
    }

    /**
     * Write a boolean.
     *
     * @param   bool $boolean Boolean.
     * @return  mixed
     */
    public function writeBoolean($boolean)
    {
        $this->write($boolean ? "1": "0", 1);
    }

    /**
     * Write an integer.
     *
     * @param   int $integer Integer.
     * @return  mixed
     */
    public function writeInteger($integer)
    {
        $this->writeString((string) $integer);
    }

    /**
     * Write a float.
     *
     * @param   float $float Float.
     * @return  mixed
     */
    public function writeFloat($float)
    {
        $this->writeString((string) $float);
    }

    /**
     * Write an array.
     *
     * @param   array $array Array.
     * @return  mixed
     */
    public function writeArray(array $array)
    {
        $this->writeString(implode(',', $array));
    }

    /**
     * Write a line.
     *
     * @param   string $line Line.
     * @return  mixed
     */
    public function writeLine($line)
    {
        $this->writeString($line."\n");
    }

    /**
     * Write all, i.e. as much as possible.
     *
     * @param   string $string String.
     * @return  mixed
     */
    public function writeAll($string)
    {
        $this->writeString($string);
    }

    /**
     * Truncate a stream to a given length.
     *
     * @param   int $size Size.
     * @return  bool
     */
    public function truncate($size)
    {
        $this->buffer = substr($this->buffer, $this->cursor, $size);
        $this->bufferLength = strlen($this->bufferLength);
        $this->cursor = 0;

        return true;
    }

    /**
     * Test for end-of-stream.
     *
     * @return  bool
     */
    public function eof()
    {
        return $this->bufferLength === $this->cursor;
    }

    /**
     * Read n characters.
     *
     * @param   int $length Length.
     * @return  string
     */
    public function read($length)
    {
        $read = substr($this->buffer, $this->cursor, $length);
        $this->cursor += $length;

        $this->ensureCursor();

        return $read;
    }

    /**
     * Alias of $this->read().
     *
     * @param   int $length Length.
     * @return  string
     */
    public function readString($length)
    {
        return $this->read($length);
    }

    /**
     * Read a character.
     * It could be equivalent to $this->read(1).
     *
     * @return  string
     */
    public function readCharacter()
    {
        return $this->read(1);
    }

    /**
     * Read a boolean.
     *
     * @return  bool
     */
    public function readBoolean()
    {
        return (bool) $this->read(1);
    }

    /**
     * Read an integer.
     *
     * @param   int $length Length.
     * @return  int
     */
    public function readInteger($length = 1)
    {
        return sprintf("%d", $this->read($length));
    }

    /**
     * Read a float.
     *
     * @param   int $length Length.
     * @return  float
     */
    public function readFloat($length = 1)
    {
        return sprintf("%f", $this->read($length));
    }

    /**
     * Read an array.
     * In most cases, it could be an alias to the $this->scanf() method.
     *
     * @param   mixed $argument Argument (because the behavior is very
     *                               different according to the implementation).
     * @return  array
     *
     * Implementation: The argument parameter is used as explode limit.
     * It reads the current line, and explode it with ','
     */
    public function readArray($argument = null)
    {
        return explode(',', $this->readLine(), $argument);
    }

    /**
     * Read a line.
     *
     * @return  string
     */
    public function readLine()
    {
        $buffer = '';

        while ("\n" !== $char = $this->readCharacter()) {
            $buffer .= $char;
        }
    }

    /**
     * Read all, i.e. read as much as possible.
     *
     * @param   int $offset Offset.
     * @return  string
     */
    public function readAll($offset = 0)
    {
        return substr($this->buffer, $offset);
    }

    /**
     * Parse input from a stream according to a format.
     *
     * @param   string $format Format (see printf's formats).
     * @return  array
     */
    public function scanf($format)
    {
        return sprintf($format, $this->readLine());
    }

    /**
     * @param int $position The position to place the cursor at
     * @param int $moveType If SEEK_ABSOLUTE, the cursor will be placed at the given position,
     *                      if SEEK_RELATIVE, it will be moved relatively to the current position
     * @return void
     */
    public function seek(int $position, int $moveType = self::SEEK_ABSOLUTE)
    {
        switch ($moveType) {
            case static::SEEK_ABSOLUTE:
                $this->cursor = $position;
                break;

            case static::SEEK_RELATIVE:
                $this->cursor += $position;

        }

        $this->ensureCursor();
    }

    public function rewind()
    {
        $this->seek(0);
    }

    public function end()
    {
        $this->seek($this->bufferLength);
    }

    private function ensureCursor()
    {
        if ($this->cursor > $this->bufferLength) {
            $this->cursor = $this->bufferLength;
        } elseif ($this->cursor < 0) {
            $this->cursor = 0;
        }
    }
}
