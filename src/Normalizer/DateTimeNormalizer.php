<?php

namespace TSantos\Serializer\Normalizer;

use TSantos\Serializer\Exception\InvalidArgumentException;
use TSantos\Serializer\SerializationContext;

/**
 * Class DateTimeNormalizer
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class DateTimeNormalizer implements NormalizerInterface
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
}
