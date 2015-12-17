<?php
declare(strict_types=1);

namespace Avalonia\Component\Message\Http;

use Avalonia\Component\Message\Exception\{
    MapperInvalidDataEofFoundException,
    MapperInvalidDataException,
    MapperMaxLineLengthException
};
use Avalonia\Component\Message\MessageMapperInterface;
use Hoa\Stream\IStream\In;
use Hoa\Stream\IStream\Out;

/**
 * Class HttpMessageMapper
 * @package Avalonia\Component\Message\Http
 * @author Benjamin Perche <benjamin@perche.me>
 */
abstract class AbstractHttpMessageMapper implements MessageMapperInterface
{
    const CR = "\r";
    const LF = "\n";
    const CRLF = self::CR.self::LF;

    const DEFAULT_MAX_LINE_LENGTH = 8192;

    /**
     * @param In $inputStream
     * @param bool $keepCrlf If true, the line ending CRLF will be returned in the string
     * @param int $offset An offset tracker for the stream
     * @param int $maxLineLength The max line length. If it is reached, an exception will be thrown
     * @return string
     *
     * @throws \Avalonia\Component\Message\Exception\MapperInvalidDataException If the line doesn't end with a CRLF, or if $maxLineLength is reached (if EOF or unix EOL is found)
     * @throws \Avalonia\Component\Message\Exception\MapperMaxLineLengthException If the line isn't ended before $maxLineLength
     *
     * Read an HTTP line
     */
    protected function readHttpLine(
        In $inputStream,
        bool $keepCrlf = false,
        int &$offset = null,
        int $maxLineLength = self::DEFAULT_MAX_LINE_LENGTH
    ): string {
        $buffer = '';
        $lastChar = '';
        $bufferLength = 0;
        $offset = $offset ?: 0;

        do {
            if ($inputStream->eof()) {
                throw (new MapperInvalidDataEofFoundException("The given line has no end. Found EOF before CRLF."))
                    ->setRemainingBufferData($buffer)
                ;
            }

            if ($maxLineLength <= $bufferLength) {
                throw new MapperMaxLineLengthException("Http line max length reached.");
            }

            $char = $inputStream->readCharacter();
            $buffer .= $char;
            $bufferLength++;
            $offset++;

            if (static::LF === $char) {
                if (static::CR !== $lastChar) {
                    throw new MapperInvalidDataException("The given line ends with a LF, not with a CRLF");
                }

                if (!$keepCrlf) {
                    $buffer = substr($buffer, 0, -2);
                }

                return $buffer;
            }

            $lastChar = $char;
        } while(true);
    }

    /**
     * @param Out $outputStream
     * @param string $line
     *
     * Writes the line ending with a CRLF on the output stream
     */
    protected function writeHttpLine(Out $outputStream, string $line)
    {
        $outputStream->writeString($line.static::CRLF);
    }
}