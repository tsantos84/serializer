<?php

declare(strict_types=1);

/*
 * This file is part of the TSantos Serializer package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TSantos\Serializer\Normalizer;

use Doctrine\Common\Persistence\Proxy;
use TSantos\Serializer\CacheableNormalizerInterface;
use TSantos\Serializer\DeserializationContext;
use TSantos\Serializer\Exception\CircularReferenceException;
use TSantos\Serializer\HydratorLoaderInterface;
use TSantos\Serializer\SerializationContext;
use TSantos\Serializer\SerializerAwareInterface;
use TSantos\Serializer\Traits\SerializerAwareTrait;

/**
 * Class ObjectNormalizer.
 */
class ObjectNormalizer implements NormalizerInterface, DenormalizerInterface, SerializerAwareInterface, CacheableNormalizerInterface
{
    use SerializerAwareTrait;

    /**
     * @var HydratorLoaderInterface
     */
    private $loader;

    /**
     * @var callable|null
     */
    private $circularReferenceHandler;

    /**
     * ObjectNormalizer constructor.
     *
     * @param HydratorLoaderInterface $classLoader
     * @param callable|null           $circularReferenceHandler
     */
    public function __construct(HydratorLoaderInterface $classLoader, callable $circularReferenceHandler = null)
    {
        $this->loader = $classLoader;
        $this->circularReferenceHandler = $circularReferenceHandler;
    }

    public function normalize($data, SerializationContext $context)
    {
        $class = \get_class($data);

        // fetches the real hydrator for Doctrine Proxies
        if ($data instanceof Proxy) {
            !$data->__isInitialized() && $data->__load();
            $class = \get_parent_class($class);
        }

        $hydrator = $this->loader->load($class);
        $objectId = \spl_object_hash($data);

        try {
            $context->enter($data, $objectId);
        } catch (CircularReferenceException $circularReferenceException) {
            if (null === $this->circularReferenceHandler) {
                throw $circularReferenceException;
            }
            return \call_user_func($this->circularReferenceHandler, $data, $circularReferenceException);
        }

        $array = $hydrator->extract($data, $context);
        $context->leave($data, $objectId);
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

        $hydrator = $this->loader->load($type);

        if (null === $object = $context->getTarget()) {
            $object = $hydrator->newInstance($data, $context);
        }

        $context->enter();
        $object = $hydrator->hydrate($object, $data, $context);
        $context->leave();

        return $object;
    }

    public function supportsDenormalization(string $type, $data, DeserializationContext $context): bool
    {
        return (\class_exists($type) || \interface_exists($type)) && \DateTime::class !== $type;
    }
}
