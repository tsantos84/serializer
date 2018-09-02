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
 * Class DummyInner.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class DummyInner extends DummyAbstract implements DummyInterface
{
    private $baz;

    private $qux;

    /**
     * DummyInner constructor.
     *
     * @param $baz
     * @param null $qux
     */
    public function __construct($baz = null, $qux = null)
    {
        $this->baz = $baz;
        $this->qux = $qux;
    }

    /**
     * @return mixed
     */
    public function getBaz()
    {
        return $this->baz;
    }

    /**
     * @param mixed $baz
     */
    public function setBaz($baz): void
    {
        $this->baz = $baz;
    }
}
