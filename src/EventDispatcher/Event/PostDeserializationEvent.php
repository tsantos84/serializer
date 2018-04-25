<?php
/**
 * This file is part of the TSantos Serializer package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TSantos\Serializer\EventDispatcher\Event;

use TSantos\Serializer\DeserializationContext;


/**
 * Class PostDeserializationEvent
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class PostDeserializationEvent extends Event
{
    /**
     * @var mixed
     */
    private $object;

    /**
     * @var DeserializationContext
     */
    private $context;

    /**
     * PostDeserializationEvent constructor.
     * @param mixed $object
     * @param DeserializationContext $context
     */
    public function __construct($object, DeserializationContext $context)
    {
        $this->object = $object;
        $this->context = $context;
    }

    /**
     * @return mixed
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * @param mixed $object
     */
    public function setObject($object): void
    {
        $this->object = $object;
    }

    /**
     * @return DeserializationContext
     */
    public function getContext(): DeserializationContext
    {
        return $this->context;
    }
}
