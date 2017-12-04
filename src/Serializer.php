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
use Tests\TSantos\Serializer\Fixture\Person;

/**
 * Class Serializer
 *
 * @package Serializer
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class Serializer implements SerializerInterface
{
    /**
     * @var EncoderRegistryInterface
     */
    private $encoders;

    /**
     * @var NormalizerRegistryInterface[]
     */
    private $normalizers;

    /**
     * @var SerializerClassLoader
     */
    private $classLoader;

    /**
     * Serializer constructor.
     * @param SerializerClassLoader $classLoader
     * @param EncoderRegistryInterface $encoders
     * @param NormalizerRegistryInterface $normalizers
     */
    public function __construct(
        SerializerClassLoader $classLoader,
        EncoderRegistryInterface $encoders,
        NormalizerRegistryInterface $normalizers
    ) {
        $this->classLoader = $classLoader;
        $this->encoders = $encoders;
        $this->normalizers = $normalizers;
    }

    /**
     * @inheritdoc
     */
    public function serialize($data, string $format, SerializationContext $context = null) : string
    {
        $encoder = $this->encoders->get($format);

        if (is_null($data) || is_scalar($data)) {
            return $this->normalize($data, $context);
        }

        return $encoder->encode($this->normalize($data, $context));
    }

    /**
     * @inheritdoc
     */
    public function normalize($data, SerializationContext $context = null)
    {
        if (is_null($data) || is_scalar($data)) {
            return $data;
        }

        if (null === $context) {
            $context = new SerializationContext();
        }

        if (!$context->isStarted()) {
            $context->start();
        }

        if ($context->isMaxDeepAchieve()) {
            return [];
        }

        if (null !== $normalizer = $this->normalizers->get($data, $context)) {
            return $normalizer->normalize($data, $context);
        }

        if (is_iterable($data)) {
            return $this->collectionToArray($data, $context);
        }

        if ($context->hasObjectProcessed($data)) {
            return [];
        }

        return $this->serializeObject($data, $context);
    }

    public function deserialize(string $content, string $type, string $format, DeserializationContext $context = null)
    {
        if (null === $context) {
            $context = new DeserializationContext();
        }

        $data = $this->encoders->get($format)->decode($content);

        return $this->denormalize($data, $type, $context);
    }

    public function denormalize(array $data, string $type, DeserializationContext $context = null)
    {
        // @todo create an object constructor interface
        $object = new $type();

        $objectSerializer = $this->classLoader->load($object, $this);

        $context->enter($object);
        $object = $objectSerializer->deserialize($object, $data, $context);
        $context->left();

        return $object;
    }

    private function serializeObject($object, SerializationContext $context): array
    {
        if ($object instanceof \JsonSerializable) {
            return $this->normalize($object->jsonSerialize(), $context);
        }

        $objectSerializer = $this->classLoader->load($object, $this);

        $context->enter($object);
        $array = $objectSerializer->serialize($object, $context);
        $context->left();

        return $array;
    }

    private function collectionToArray($data, SerializationContext $context): array
    {
        $context->enter();
        $array = [];
        foreach ($data as $key => $value) {
            if (is_scalar($value)) {
                $array[$key] = $value;
                continue;
            }
            $array[$key] = $this->normalize($value, $context);
        }
        $context->left();

        return $array;
    }
}
