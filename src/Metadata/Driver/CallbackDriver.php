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

namespace TSantos\Serializer\Metadata\Driver;

use Metadata\Driver\DriverInterface;
use TSantos\Serializer\Metadata\ClassMetadata;

/**
 * Class CallbackDriver.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class CallbackDriver implements DriverInterface
{
    /**
     * @var callable
     */
    private $callback;

    /**
     * CallbackDriver constructor.
     *
     * @param callable $callback
     */
    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    public function loadMetadataForClass(\ReflectionClass $class)
    {
        $metadata = \call_user_func($this->callback, $class);

        if (!$metadata instanceof ClassMetadata) {
            throw new \BadMethodCallException(
                \sprintf(
                    'The metadata callback should return an instance of %s, %s given',
                    ClassMetadata::class,
                    \is_object($metadata) ? \get_class($metadata) : \gettype($metadata)
                )
            );
        }

        return $metadata;
    }
}
