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

use Symfony\Component\EventDispatcher\Event;
use TSantos\Serializer\SerializationContext;

/**
 * Class PostSerializationEvent
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class PostSerializationEvent extends Event
{
    /**
     * @var array
     */
    private $data;

    /**
     * @var SerializationContext
     */
    private $context;

    /**
     * PreSerializationEvent constructor.
     * @param mixed $data
     * @param SerializationContext $context
     */
    public function __construct(array $data, SerializationContext $context)
    {
        $this->data = $data;
        $this->context = $context;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array $data
     * @return PostSerializationEvent
     */
    public function setData(array $data): PostSerializationEvent
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
