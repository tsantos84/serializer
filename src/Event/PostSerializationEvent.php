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
 * Class PostSerializationEvent.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class PostSerializationEvent extends Event
{
    /**
     * @var mixed
     */
    private $data;

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
     * @param mixed $data
     * @param SerializationContext $context
     * @param string $type
     */
    public function __construct($data, SerializationContext $context, string $type)
    {
        $this->data = $data;
        $this->context = $context;
        $this->type = $type;
    }

    /**
     * @return mixed
     * @codeCoverageIgnore
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     *
     * @return PostSerializationEvent
     * @codeCoverageIgnore
     */
    public function setData($data): self
    {
        $this->data = $data;

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
