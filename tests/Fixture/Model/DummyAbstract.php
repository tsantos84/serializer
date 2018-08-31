<?php

namespace Tests\TSantos\Serializer\Fixture\Model;

/**
 * Class DummyAbstract
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
abstract class DummyAbstract
{
    private $foobar;

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
