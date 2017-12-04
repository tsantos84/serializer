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
 * Class SerializationContext
 *
 * @package Serializer
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class DeserializationContext extends AbstractContext
{
    /**
     * @var object
     */
    private $target;

    /**
     * @return object
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * @param object $target
     */
    public function setTarget(object $target): void
    {
        $this->target = $target;
    }
}
