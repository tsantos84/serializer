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
     * PreSerializationEvent constructor.
     *
     * @param mixed                $data
     * @param SerializationContext $context
     */
    public function __construct($data, SerializationContext $context)
    {
        $this->data = $data;
        $this->context = $context;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     *
     * @return PostSerializationEvent
     */
    public function setData($data): PostSerializationEvent
    {
        $this->data = $data;

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
