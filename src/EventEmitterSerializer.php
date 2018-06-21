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

use TSantos\Serializer\Encoder\EncoderInterface;
use TSantos\Serializer\Event\PostDeserializationEvent;
use TSantos\Serializer\Event\PostSerializationEvent;
use TSantos\Serializer\Event\PreDeserializationEvent;
use TSantos\Serializer\Event\PreSerializationEvent;
use TSantos\Serializer\EventDispatcher\EventDispatcherInterface;

/**
 * Class EventEmitterSerializer
 *
 * Dispatches some events in serialization and deserialization operations
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class EventEmitterSerializer extends Serializer
{
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * EventEmitterSerializer constructor.
     * @param EncoderInterface $encoder
     * @param NormalizerRegistryInterface $normalizers
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(
        EncoderInterface $encoder,
        NormalizerRegistryInterface $normalizers,
        EventDispatcherInterface $dispatcher
    ) {
        parent::__construct($encoder, $normalizers);
        $this->dispatcher = $dispatcher;
    }

    public function normalize($data, SerializationContext $context = null)
    {
        if (null === $context) {
            $context = new SerializationContext();
        }

        if (!$context->isStarted()) {
            $context->start();
        }

        $type = is_object($data) ? get_class($data) : gettype($data);

        $event = new PreSerializationEvent($data, $context);
        $this->dispatcher->dispatch(Events::PRE_SERIALIZATION, $event, $type);

        $normalized = parent::normalize($event->getObject(), $context);

        $event = new PostSerializationEvent($normalized, $context);
        $this->dispatcher->dispatch(Events::POST_SERIALIZATION, $event, $type);

        return $event->getData();
    }

    public function denormalize($data, string $type, DeserializationContext $context = null)
    {
        if (null === $context) {
            $context = new DeserializationContext();
        }

        if (!$context->isStarted()) {
            $context->start();
        }

        $event = new PreDeserializationEvent($data, $context);
        $this->dispatcher->dispatch(Events::PRE_DESERIALIZATION, $event, $type);

        $denormalized = parent::denormalize($event->getData(), $type, $context);

        $event = new PostDeserializationEvent($denormalized, $context);
        $this->dispatcher->dispatch(Events::POST_DESERIALIZATION, $event, $type);

        return $event->getObject();
    }
}
