<?php

namespace Tests\TSantos\Serializer\Fixture;

/**
 * Class Employee
 *
 * @package Tests\TSantos\Serializer\Fixture
 * @author Tales Santos <tales.maxmilhas@gmail.com>
 */
class Employee extends Person
{
    /**
     * @var string
     */
    private $position;

    /**
     * @return string
     */
    public function getPosition(): string
    {
        return $this->position;
    }

    /**
     * @param string $position
     * @return Employee
     */
    public function setPosition(string $position): Employee
    {
        $this->position = $position;
        return $this;
    }
}
