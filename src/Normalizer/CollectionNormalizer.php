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

use TSantos\Serializer\CacheableNormalizerInterface;
use TSantos\Serializer\DeserializationContext;
use TSantos\Serializer\SerializationContext;
use TSantos\Serializer\SerializerAwareInterface;
use TSantos\Serializer\Traits\SerializerAwareTrait;

/**
 * Class CollectionNormalizer.
 */
class CollectionNormalizer implements NormalizerInterface, DenormalizerInterface, SerializerAwareInterface, CacheableNormalizerInterface
{
    use SerializerAwareTrait;

    public function normalize($data, SerializationContext $context)
    {
        $array = [];
        $context->enter();
        foreach ($data as $key => $value) {
            if (\is_scalar($value)) {
                $array[$key] = $value;
                continue;
            }
            $array[$key] = $this->serializer->normalize($value, $context);
        }
        $context->leave();

        return $array;
    }

    public function supportsNormalization($data, SerializationContext $context): bool
    {
        return \is_iterable($data);
    }

    public function denormalize($data, string $type, DeserializationContext $context)
    {
        $type = \mb_substr($type, 0, \mb_strpos($type, '[]'));

        $scalarTypes = [
            'integer' => true,
            'string' => true,
            'float' => true,
            'double' => true,
            'boolean' => true,
            'mixed' => true,
        ];

        if (isset($scalarTypes[$type])) {
            foreach ($data as $key => $val) {
                if (null === $val) {
                    continue;
                }
                switch ($type) {
                    case 'string':
                        $data[$key] = (string) $val;
                        break;
                    case 'integer':
                        $data[$key] = (int) $val;
                        break;
                    case 'float':
                    case 'double':
                        $data[$key] = (float) $val;
                        break;
                    case 'boolean':
                        $data[$key] = (bool) $val;
                        break;
                    case 'mixed':
                    default:
                        $data[$key] = $val;
                        break;
                }
            }

            return $data;
        }

        $result = [];
        foreach ($data as $key => $item) {
            $result[$key] = $this->serializer->denormalize($item, $type, $context);
        }

        return $result;
    }

    public function supportsDenormalization(string $type, $data, DeserializationContext $context): bool
    {
        return \mb_strpos($type, '[]') > 0;
    }

    public function canBeCachedByType(): bool
    {
        return true;
    }
}
