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

use TSantos\Serializer\Exception\InvalidArgumentException;
use TSantos\Serializer\SerializationContext;

/**
 * Class IdentityNormalizer
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class IdentityNormalizer implements NormalizerInterface
{
    public function normalize($data, SerializationContext $context)
    {
        if (!$data instanceof IdentifiableInterface) {
            throw new InvalidArgumentException('Data should be instance of ' . IdentifiableInterface::class);
        }

        return $data->getId();
    }

    public function supportsNormalization($data, SerializationContext $context): bool
    {
        return $data instanceof IdentifiableInterface;
    }
}
