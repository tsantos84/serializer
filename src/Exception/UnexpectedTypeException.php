<?php

namespace TSantos\Serializer\Exception;

/**
 * Class UnexpectedTypeException
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class UnexpectedTypeException extends InvalidArgumentException
{
    public static function createKeyTypeException(string $expected, string $given): self
    {
        $message = \sprintf('Expected key type "%s", "%s" given', $expected, $given);
        return new self($message);
    }
}
