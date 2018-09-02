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
 * Class DummyAbstract.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
abstract class DummyAbstract
{
    private $foobar;

    private $foo;

    /**
     * @return mixed
     */
    public function getFoobar()
    {
        return $this->foobar;
    }

    /**
     * @param mixed $foobar
     */
    public function setFoobar($foobar): void
    {
        $this->foobar = $foobar;
    }
}
