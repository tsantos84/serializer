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
use TSantos\Serializer\ObjectInstantiator\ObjectInstantiatorInterface;

/**
 * Class Serializer
 *
 * @package Serializer
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class Serializer implements SerializerInterface
{
    /**
     * @var EncoderInterface
     */
    private $encoder;

    /**
     * @var NormalizerRegistryInterface[]
     */
    private $normalizers;

    /**
     * @var SerializerClassLoader
     */
    private $classLoader;

    /**
     * @var ObjectInstantiatorInterface
     */
    private $instantiator;

    /**
     * Serializer constructor.
     * @param SerializerClassLoader $classLoader
     * @param EncoderInterface $encoder
     * @param NormalizerRegistryInterface $normalizers
     * @param ObjectInstantiatorInterface $instantiator
     */
    public function __construct(
        SerializerClassLoader $classLoader,
        EncoderInterface $encoder,
        NormalizerRegistryInterface $normalizers,
        ObjectInstantiatorInterface $instantiator
    ) {
        $this->classLoader = $classLoader;
        $this->encoder = $encoder;
        $this->instantiator = $instantiator;

        foreach ($normalizers as $normalizer) {
            if ($normalizer instanceof SerializerAwareInterface) {
                $normalizer->setSerializer($this);
            }
        }

        $this->normalizers = $normalizers;
    }

    /**
     * @inheritdoc
     */
    public function serialize($data, SerializationContext $context = null): string
    {
        if (is_null($data) || is_scalar($data)) {
            return $this->normalize($data, $context);
        }

        return $this->encoder->encode($this->normalize($data, $context));
    }

    /**
     * @inheritdoc
     */
    public function normalize($data, SerializationContext $context = null)
    {
        if (null === $context) {
            $context = new SerializationContext();
        }

        if (!$context->isStarted()) {
            $context->start();
        }

        if ($context->isMaxDeepAchieve()) {
            return [];
        }

        if (null !== $normalizer = $this->normalizers->getNormalizer($data, $context)) {
            return $normalizer->normalize($data, $context);
        }

        throw new \RuntimeException('There is no normalizer able to normalize the data given');
    }

    /**
     * @inheritdoc
     */
    public function deserialize(string $content, string $type, DeserializationContext $context = null)
    {
        $data = $this->encoder->decode($content);
        return $this->denormalize($data, $type, $context);
    }

    /**
     * @inheritdoc
     */
    public function denormalize($data, string $type, DeserializationContext $context = null)
    {
        if (null === $context) {
            $context = new DeserializationContext();
        }

        if (!$context->isStarted()) {
            $context->start();
        }

        if (empty($data)) {
            return [];
        }

        if (null !== $normalizer = $this->normalizers->getDenormalizer($data, $type, $context)) {
            return $normalizer->denormalize($data, $context);
        }

        if ($type === 'array') {
            $type = is_scalar(reset($data)) ? gettype(reset($data)) : 'string';
            return $this->denormalizeCollection($data, $type, $context);
        }

        if (false === $open = strpos($type, '<')) {
            if (null === $object = $context->getTarget()) {
                $object = $this->instantiator->create($type, $data, $context);
            }

            $objectSerializer = $this->classLoader->load($type, $this);
            $context->enter($object);
            $object = $objectSerializer->deserialize($object, $data, $context);
            $context->left();
            return $object;
        }

        $close = strpos($type, '>', -1) - 6;
        $innerType = substr($type, $open + 1, $close);
        return $this->denormalizeCollection($data, $innerType, $context);
    }

    private function denormalizeCollection(iterable $data, string $type, DeserializationContext $context)
    {
        $result = [];

        $scalarTypes = [
            'integer' => 'int',
            'string' => 'str',
            'float' => 'float',
            'double' => 'double',
            'boolean' => 'bool'
        ];

        $callback = function ($item) use ($type, $context) {
            return $this->denormalize($item, $type, $context);
        };

        if (isset($scalarTypes[$type])) {
            $type = $scalarTypes[$type];
            $callback = function ($item) use ($type) {
                return call_user_func($type . 'val', $item);
            };
        }

        foreach ($data as $key => $item) {
            $result[$key] = $callback($item);
        }

        return $result;
    }
}
