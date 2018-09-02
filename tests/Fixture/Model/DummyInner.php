<?php

namespace Tests\TSantos\Serializer\Fixture\Model;

/**
 * Class DummyInner
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class DummyInner extends DummyAbstract implements DummyInterface
{
    private $baz;

    private $qux;

    /**
     * DummyInner constructor.
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
