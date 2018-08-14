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
     * @var string
     */
    private $type;

    /**
     * PreSerializationEvent constructor.
     *
     * @param mixed                $data
     * @param SerializationContext $context
     * @param string               $type
     */
    public function __construct($data, SerializationContext $context, string $type)
    {
        $this->object = $data;
        $this->context = $context;
        $this->type = $type;
    }

    /**
     * @return object
     * @codeCoverageIgnore
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * @param $object
     *
     * @return PreSerializationEvent
     * @codeCoverageIgnore
     */
    public function setObject($object): self
    {
        $this->object = $object;

        return $this;
    }

    /**
     * @return SerializationContext
     * @codeCoverageIgnore
     */
    public function getContext(): SerializationContext
    {
        return $this->context;
    }

    /**
     * @return string
     * @codeCoverageIgnore
     */
    public function getType(): string
    {
        return $this->type;
    }
}
