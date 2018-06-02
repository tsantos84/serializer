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

use TSantos\Serializer\Normalizer\DenormalizerInterface;
use TSantos\Serializer\Normalizer\NormalizerInterface;

/**
 * Interface NormalizerRegistryInterface
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
interface NormalizerRegistryInterface extends \IteratorAggregate
{
    /**
     * @param $normalizer
     * @return $this
     */
    public function add($normalizer);

    /**
     * Adds a normalizer at the beginning
     *
     * @param $normalizer
     * @return mixed
     */
    public function unshift($normalizer);

    /**
     * @param mixed $data
     * @param SerializationContext $context
     * @return NormalizerInterface
     */
    public function getNormalizer($data, SerializationContext $context): ?NormalizerInterface;

    /**
     * @param $data
     * @param string $type
     * @param DeserializationContext $context
     * @return DenormalizerInterface
     */
    public function getDenormalizer($data, string $type, DeserializationContext $context): ?DenormalizerInterface;
}
