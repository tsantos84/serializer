<?php
/**
 * This file is part of the TSantos Serializer package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\TSantos\Serializer\Fixture\Model;

/**
 * Class DummyCollection
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class DummyCollection
{
    /**
     * @var array
     */
    private $foo = [];

    /**
     * @var array
     */
    private $bar = [];

    /**
     * @return array
     */
    public function getFoo(): array
    {
        return $this->foo;
    }

    /**
     * @param array $foo
     */
    public function setFoo(array $foo): void
    {
        $this->foo = $foo;
    }
}
