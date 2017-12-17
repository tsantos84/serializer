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
 * Class PreSerializationEvent
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class PreSerializationEvent extends Event
{
    /**
     * @var object
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
    public function __construct($data, SerializationContext $context)
    {
        $this->data = $data;
        $this->context = $context;
    }

    /**
     * @return object
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param object $data
     * @return PreSerializationEvent
     */
    public function setData($data): PreSerializationEvent
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
