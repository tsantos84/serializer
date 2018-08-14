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

use TSantos\Serializer\DeserializationContext;

/**
 * Class PostDeserializationEvent.
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
     * @var string
     */
    private $type;

    /**
     * PostDeserializationEvent constructor.
     *
     * @param mixed $object
     * @param DeserializationContext $context
     * @param string $type
     */
    public function __construct($object, DeserializationContext $context, string $type)
    {
        $this->object = $object;
        $this->context = $context;
        $this->type = $type;
    }

    /**
     * @return mixed
     * @codeCoverageIgnore
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * @param mixed $object
     * @codeCoverageIgnore
     */
    public function setObject($object): void
    {
        $this->object = $object;
    }

    /**
     * @return DeserializationContext
     * @codeCoverageIgnore
     */
    public function getContext(): DeserializationContext
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
