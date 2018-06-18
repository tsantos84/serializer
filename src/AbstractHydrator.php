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

/**
 * Class AbstractSerializerClass
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
abstract class AbstractHydrator implements HydratorInterface
{
    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * AbstractSerializerClass constructor.
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }
}
