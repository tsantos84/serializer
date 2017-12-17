<?php
/**
 * This file is part of the TSantos Serializer package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TSantos\Serializer;
use TSantos\Serializer\EventDispatcher\EventDispatcherInterface;

/**
 * Class AbstractSerializerClass
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
abstract class AbstractSerializerClass implements SerializerClassInterface
{
    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @var \SplObjectStorage
     */
    protected $computedGroupKeys;

    /**
     * AbstractSerializerClass constructor.
     * @param SerializerInterface $serializer
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(SerializerInterface $serializer, EventDispatcherInterface $dispatcher)
    {
        $this->serializer = $serializer;
        $this->computedGroupKeys = new \SplObjectStorage();
        $this->dispatcher = $dispatcher;
    }
}
