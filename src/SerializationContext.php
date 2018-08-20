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

namespace TSantos\Serializer;

use TSantos\Serializer\Exception\CircularReferenceException;

/**
 * Class SerializationContext.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class SerializationContext extends AbstractContext
{
    /** @var array */
    private $circularReference = [];

    /** @var int */
    private $circularReferenceCount = 1;

    /**
     * @param int $circularReferenceCount
     *
     * @return SerializationContext
     */
    public function setCircularReferenceCount(int $circularReferenceCount): self
    {
        $this->circularReferenceCount = $circularReferenceCount;

        return $this;
    }

    /**
     * @param null $object
     */
    public function enter($object = null, string $id = null)
    {
        parent::enter();

        if (!\is_object($object)) {
            return;
        }

        if (!isset($this->circularReference[$id])) {
            $this->circularReference[$id] = 1;

            return;
        }

        if (++$this->circularReference[$id] > $this->circularReferenceCount) {
            $objectName = \method_exists($object, '__toString') ? $object->__toString() : $id;
            throw new CircularReferenceException(
                \sprintf(
                    'A circular reference for object "%s" of class "%s" was detected',
                    $objectName,
                    \get_class($object)
                )
            );
        }
    }

    /**
     * @param null $object
     */
    public function leave($object = null, string $id = null)
    {
        parent::leave();

        if (!\is_object($object)) {
            return;
        }

        if (isset($this->circularReference)) {
            --$this->circularReference[$id];
        }
    }
}
