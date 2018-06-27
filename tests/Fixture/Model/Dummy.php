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
 * Class Dummy.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class Dummy
{
    /**
     * @var string
     */
    private $foo;

    /**
     * @var string
     */
    private $bar;

    /**
     * @var string
     */
    private $baz;

    /**
     * @var Dummy
     */
    private $innerDummy;

    /**
     * ClassWithoutAccessor constructor.
     *
     * @param string $foo
     */
    public function __construct(string $foo)
    {
        $this->foo = $foo;
    }

    /**
     * @return string
     */
    public function getFoo(): string
    {
        return $this->foo;
    }

    /**
     * @return string
     */
    public function getBar(): ?string
    {
        return $this->bar;
    }

    /**
     * @param string $bar
     */
    public function setBar(string $bar): void
    {
        $this->bar = $bar;
    }

    /**
     * @param string $baz
     */
    public function setBaz(string $baz): void
    {
        $this->baz = $baz;
    }

    public function getFooBar(): string
    {
        return $this->foo.$this->bar;
    }
}
