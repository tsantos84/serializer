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

namespace TSantos\Serializer;

/**
 * Class SerializationContext.
 *
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
     * @param $target
     *
     * @return DeserializationContext
     */
    public function setTarget($target): self
    {
        if (!\is_object($target)) {
            throw new \InvalidArgumentException('The $target should be an object, '.\gettype($target).' given');
        }

        $this->target = $target;

        return $this;
    }
}
