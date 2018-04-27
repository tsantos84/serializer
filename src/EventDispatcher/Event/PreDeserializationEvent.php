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
 * Class PreDeserializationEvent
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class PreDeserializationEvent extends Event
{
    /**
     * @var array
     */
    private $data;

    /**
     * @var DeserializationContext
     */
    private $context;

    /**
     * PreDeserializationEvent constructor.
     * @param array $data
     * @param DeserializationContext $context
     */
    public function __construct(array $data, DeserializationContext $context)
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
     */
    public function setData(array $data): void
    {
        $this->data = $data;
    }

    /**
     * @return DeserializationContext
     */
    public function getContext(): DeserializationContext
    {
        return $this->context;
    }
}
