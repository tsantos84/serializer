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

namespace TSantos\Serializer\Normalizer;

use TSantos\Serializer\CacheableNormalizerInterface;
use TSantos\Serializer\DeserializationContext;
use TSantos\Serializer\ObjectInstantiator\ObjectInstantiatorInterface;
use TSantos\Serializer\SerializationContext;
use TSantos\Serializer\SerializerAwareInterface;
use TSantos\Serializer\HydratorLoader;
use TSantos\Serializer\Traits\SerializerAwareTrait;

/**
 * Class ObjectNormalizer.
 */
class ObjectNormalizer implements NormalizerInterface, DenormalizerInterface, SerializerAwareInterface, CacheableNormalizerInterface
{
    use SerializerAwareTrait;

    /**
     * @var HydratorLoader
     */
    private $loader;

    /**
     * @var ObjectInstantiatorInterface
     */
    private $instantiator;

    /**
     * ObjectNormalizer constructor.
     *
     * @param HydratorLoader              $classLoader
     * @param ObjectInstantiatorInterface $instantiator
     */
    public function __construct(HydratorLoader $classLoader, ObjectInstantiatorInterface $instantiator)
    {
        $this->loader = $classLoader;
        $this->instantiator = $instantiator;
    }

    public function normalize($data, SerializationContext $context)
    {
        $hydrator = $this->loader->load(\get_class($data), $this->serializer);

        $context->enter($data);
        $array = $hydrator->extract($data, $context);
        $context->leave($data);

        return $array;
    }

    public function canBeCachedByType(): bool
    {
        return true;
    }

    public function supportsNormalization($data, SerializationContext $context): bool
    {
        return \is_object($data) && !\is_iterable($data) && !$data instanceof \DateTimeInterface;
    }

    public function denormalize($data, string $type, DeserializationContext $context)
    {
        if (empty($data)) {
            return [];
        }

        if (null === $object = $context->getTarget()) {
            $object = $this->instantiator->create($type, $data, $context);
        }

        $objectSerializer = $this->loader->load($type, $this->serializer);
        $context->enter();
        $object = $objectSerializer->hydrate($object, $data, $context);
        $context->leave();

        return $object;
    }

    public function supportsDenormalization(string $type, $data, DeserializationContext $context): bool
    {
        return \class_exists($type) && \DateTime::class !== $type;
    }
}
