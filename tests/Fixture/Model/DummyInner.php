<?php

namespace Tests\TSantos\Serializer\Fixture\Model;

/**
 * Class DummyInner
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class DummyInner extends DummyAbstract
{
    private $baz;

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
