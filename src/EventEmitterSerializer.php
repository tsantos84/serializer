<?php

namespace TSantos\Serializer;

use TSantos\Serializer\Encoder\EncoderInterface;
use TSantos\Serializer\EventDispatcher\Event\PostDeserializationEvent;
use TSantos\Serializer\EventDispatcher\Event\PostSerializationEvent;
use TSantos\Serializer\EventDispatcher\Event\PreDeserializationEvent;
use TSantos\Serializer\EventDispatcher\Event\PreSerializationEvent;
use TSantos\Serializer\EventDispatcher\EventDispatcherInterface;
use TSantos\Serializer\EventDispatcher\SerializerEvents;
use TSantos\Serializer\ObjectInstantiator\ObjectInstantiatorInterface;

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
     * Serializer constructor.
     * @param SerializerClassLoader $classLoader
     * @param EncoderInterface $encoder
     * @param NormalizerRegistryInterface $normalizers
     * @param ObjectInstantiatorInterface $instantiator
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(
        SerializerClassLoader $classLoader,
        EncoderInterface $encoder,
        NormalizerRegistryInterface $normalizers,
        ObjectInstantiatorInterface $instantiator,
        EventDispatcherInterface $dispatcher
    ) {
        parent::__construct($classLoader, $encoder, $normalizers, $instantiator);
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
        $this->dispatcher->dispatch(SerializerEvents::PRE_SERIALIZATION, $event, $type);

        $normalized = parent::normalize($event->getObject(), $context);

        $event = new PostSerializationEvent($normalized, $context);
        $this->dispatcher->dispatch(SerializerEvents::POST_SERIALIZATION, $event, $type);

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
        $this->dispatcher->dispatch(SerializerEvents::PRE_DESERIALIZATION, $event, $type);

        $denormalized = parent::denormalize($event->getData(), $context);

        $event = new PostDeserializationEvent($denormalized, $context);
        $this->dispatcher->dispatch(SerializerEvents::POST_DESERIALIZATION, $event, $type);

        return $event->getObject();
    }
}
