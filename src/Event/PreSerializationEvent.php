<?php

declare(strict_types=1);
/**
 * This file is part of the TSantos Serializer package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TSantos\Serializer\Event;

use TSantos\Serializer\SerializationContext;

/**
 * Class PreSerializationEvent.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class PreSerializationEvent extends Event
{
    /**
     * @var object
     */
    private $object;

    /**
     * @var SerializationContext
     */
    private $context;

    /**
     * PreSerializationEvent constructor.
     *
     * @param mixed                $data
     * @param SerializationContext $context
     */
    public function __construct($data, SerializationContext $context)
    {
        $this->object = $data;
        $this->context = $context;
    }

    /**
     * @return object
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * @param $object
     *
     * @return PreSerializationEvent
     */
    public function setObject($object): PreSerializationEvent
    {
        $this->object = $object;

        return $this;
    }

    /**
     * @return SerializationContext
     */
    public function getContext(): SerializationContext
    {
        return $this->context;
    }
}
