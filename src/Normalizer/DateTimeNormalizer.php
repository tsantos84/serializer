<?php
/**
 * This file is part of the TSantos Serializer package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TSantos\Serializer\Normalizer;

use TSantos\Serializer\DeserializationContext;
use TSantos\Serializer\Exception\InvalidArgumentException;
use TSantos\Serializer\SerializationContext;

/**
 * Class DateTimeNormalizer
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class DateTimeNormalizer implements NormalizerInterface, DenormalizerInterface
{
    /**
     * @var string
     */
    private $format;

    /**
     * DateTimeNormalizer constructor.
     * @param string $format
     */
    public function __construct(string $format = \DateTime::ATOM)
    {
        $this->format = $format;
    }

    public function normalize($data, SerializationContext $context)
    {
        if (!$data instanceof \DateTimeInterface) {
            throw new InvalidArgumentException('Data should be instance of ' . \DateTimeInterface::class);
        }

        return $data->format($this->format);
    }

    public function supportsNormalization($data, SerializationContext $context): bool
    {
        return $data instanceof \DateTimeInterface;
    }

    public function denormalize($data, DeserializationContext $context)
    {
        return \DateTime::createFromFormat($this->format, $data);
    }

    public function supportsDenormalization(string $type, $data, DeserializationContext $context): bool
    {
        return $type === \DateTime::class || $type === \DateTimeInterface::class;
    }
}
