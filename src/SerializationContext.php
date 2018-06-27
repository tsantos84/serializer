<?php
/**
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
    /** @var bool */
    private $serializeNull = false;

    /** @var array */
    private $circularReference = [];

    /** @var int */
    private $circularReferenceCount = 1;

    /**
     * @param bool $enabled
     *
     * @return SerializationContext
     */
    public function setSerializeNull(bool $enabled): self
    {
        $this->serializeNull = $enabled;

        return $this;
    }

    /**
     * @return bool
     */
    public function shouldSerializeNull(): bool
    {
        return $this->serializeNull;
    }

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
    public function enter($object = null)
    {
        parent::enter();

        if (!is_object($object)) {
            return;
        }

        $hash = \spl_object_hash($object);

        if (!isset($this->circularReference[$hash])) {
            $this->circularReference[$hash] = 1;

            return;
        }

        if (++$this->circularReference[$hash] > $this->circularReferenceCount) {
            throw new CircularReferenceException(
                sprintf('A circular reference for object of class %s was detected', get_class($object))
            );
        }
    }

    /**
     * @param null $object
     */
    public function leave($object = null)
    {
        parent::leave();

        if (!is_object($object)) {
            return;
        }

        $hash = \spl_object_hash($object);

        if (isset($this->circularReference)) {
            --$this->circularReference[$hash];
        }
    }
}
