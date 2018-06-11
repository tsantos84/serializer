<?php
/**
 * This file is part of the TSantos Serializer package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TSantos\Serializer\Traits;

use TSantos\Serializer\SerializerInterface;

/**
 * Trait SerializerAwareTrait
 * @package TSantos\Serializer\Traits
 */
trait SerializerAwareTrait
{
    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @param SerializerInterface $serializer
     */
    public function setSerializer(SerializerInterface $serializer): void
    {
        $this->serializer = $serializer;
    }
}
