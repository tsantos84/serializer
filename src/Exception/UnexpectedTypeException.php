<?php

declare(strict_types=1);

/*
 * This file is part of the TSantos Serializer package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TSantos\Serializer\Exception;

/**
 * Class UnexpectedTypeException.
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
