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
 * Class PreDeserializationEvent.
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
     * @var string
     */
    private $type;

    /**
     * PreDeserializationEvent constructor.
     *
     * @param array $data
     * @param DeserializationContext $context
     * @param string $type
     */
    public function __construct(array $data, DeserializationContext $context, string $type)
    {
        $this->data = $data;
        $this->context = $context;
        $this->type = $type;
    }

    /**
     * @return array
     * @codeCoverageIgnore
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array $data
     * @codeCoverageIgnore
     */
    public function setData(array $data): void
    {
        $this->data = $data;
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
