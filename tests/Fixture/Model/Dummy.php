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

namespace Tests\TSantos\Serializer\Fixture\Model;

/**
 * Class Dummy.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class Dummy extends DummyAbstract
{
    private $foo;

    private $bar;

    /**
     * Dummy constructor.
     * @param $foo
     * @param $bar
     */
    public function __construct($foo = null, $bar = null)
    {
        $this->foo = $foo;
        $this->bar = $bar;
    }

    /**
     * @return mixed
     */
    public function getFoo()
    {
        return $this->foo;
    }

    /**
     * @param mixed $foo
     */
    public function setFoo($foo): void
    {
        $this->foo = $foo;
    }
}
