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

use TSantos\Serializer\CacheableNormalizerInterface;
use TSantos\Serializer\DeserializationContext;
use TSantos\Serializer\SerializationContext;
use TSantos\Serializer\SerializerAwareInterface;
use TSantos\Serializer\Traits\SerializerAwareTrait;

/**
 * Class CollectionNormalizer
 * @package TSantos\Serializer\Normalizer
 */
class CollectionNormalizer implements
    NormalizerInterface,
    DenormalizerInterface,
    SerializerAwareInterface,
    CacheableNormalizerInterface
{
    use SerializerAwareTrait;

    public function normalize($data, SerializationContext $context)
    {
        $context->enter();
        $array = [];
        foreach ($data as $key => $value) {
            if (is_scalar($value)) {
                $array[$key] = $value;
                continue;
            }
            $array[$key] = $this->serializer->normalize($value, $context);
        }
        $context->left();

        return $array;
    }

    public function supportsNormalization($data, SerializationContext $context): bool
    {
        return is_iterable($data);
    }

    public function denormalize($data, string $type, DeserializationContext $context)
    {
        $type = substr($type, 0, strpos($type, '[]'));

        $scalarTypes = [
            'integer' => true,
            'string' => true,
            'float' => true,
            'double' => true,
            'boolean' => true
        ];

        if (isset($scalarTypes[$type])) {
            foreach ($data as $key => $val) {
                if ($val === null) {
                    continue;
                }
                switch ($type) {
                    case 'string':
                        $data[$key] = (string)$val;
                        continue;
                    case 'integer':
                        $data[$key] = (integer)$val;
                        continue;
                    case 'float':
                        $data[$key] = (float)$val;
                        continue;
                    case 'double':
                        $data[$key] = (double)$val;
                        continue;
                    case 'boolean':
                        $data[$key] = (boolean)$val;
                        continue;
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
        return strpos($type, '[]') > 0 || 'array' === $type;
    }

    public function canBeCachedByType(): bool
    {
        return true;
    }
}
