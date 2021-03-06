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

namespace TSantos\Serializer;

use TSantos\Serializer\Encoder\EncoderInterface;
use TSantos\Serializer\Exception\DenormalizerNotFoundException;
use TSantos\Serializer\Exception\NormalizerNotFoundException;

/**
 * Class Serializer.
 *
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
     * Serializer constructor.
     *
     * @param EncoderInterface            $encoder
     * @param NormalizerRegistryInterface $normalizers
     */
    public function __construct(
        EncoderInterface $encoder,
        NormalizerRegistryInterface $normalizers
    ) {
        $this->encoder = $encoder;

        foreach ($normalizers as $normalizer) {
            if ($normalizer instanceof SerializerAwareInterface) {
                $normalizer->setSerializer($this);
            }
        }

        $this->normalizers = $normalizers;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize($data, SerializationContext $context = null)
    {
        if (null === $data || \is_scalar($data)) {
            return $data;
        }

        return $this->encoder->encode($this->normalize($data, $context));
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($data, SerializationContext $context = null)
    {
        if (null === $context) {
            $context = new SerializationContext();
        }

        if (!$context->isStarted()) {
            $context->start();
        }

        if (null !== $normalizer = $this->normalizers->getNormalizer($data, $context)) {
            return $normalizer->normalize($data, $context);
        }

        throw new NormalizerNotFoundException(
            \sprintf(
                'There is no normalizer able to normalize the data of type %s',
                \is_object($data) ? \get_class($data) : \gettype($data)
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function deserialize(string $content, string $type, DeserializationContext $context = null)
    {
        $data = $this->encoder->decode($content);

        return $this->denormalize($data, $type, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, string $type, DeserializationContext $context = null)
    {
        if (null === $context) {
            $context = new DeserializationContext();
        }

        if (!$context->isStarted()) {
            $context->start();
        }

        if (null !== $normalizer = $this->normalizers->getDenormalizer($data, $type, $context)) {
            return $normalizer->denormalize($data, $type, $context);
        }

        throw new DenormalizerNotFoundException('There is no denormalizer able to denormalize data of type '.$type);
    }
}
