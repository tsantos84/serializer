<?php

/*
 * This file is part of the TSantos Serializer package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


/**
 * Class NoNamespaceDummy
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class NoNamespaceDummy
{
    public $foo;

    public $bar;

    /**
     * NoNamespaceDummy constructor.
     * @param $foo
     * @param $bar
     */
    public function __construct($foo, $bar)
    {
        $this->foo = $foo;
        $this->bar = $bar;
    }
}
