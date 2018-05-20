<?php

namespace TSantos\Serializer\Normalizer;

use TSantos\Serializer\DeserializationContext;
use TSantos\Serializer\SerializationContext;
use TSantos\Serializer\SerializerAwareInterface;
use TSantos\Serializer\Traits\SerializerAwareTrait;

/**
 * Class CollectionNormalizer
 * @package TSantos\Serializer\Normalizer
 */
class CollectionNormalizer implements NormalizerInterface, DenormalizerInterface, SerializerAwareInterface
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
        if ($type === '[]') {
            $type = is_scalar(reset($data)) ? gettype(reset($data)) : 'string';
        } else {
            $type = substr($type, 0, strpos($type, '[]'));
        }

        $result = [];

        $scalarTypes = [
            'integer' => 'int',
            'string' => 'str',
            'float' => 'float',
            'double' => 'double',
            'boolean' => 'bool'
        ];

        $callback = function ($item) use ($type, $context) {
            return $this->serializer->denormalize($item, $type, $context);
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

    public function supportsDenormalization(string $type, $data, DeserializationContext $context): bool
    {
        return strpos($type, '[]') !== false;
    }
}
