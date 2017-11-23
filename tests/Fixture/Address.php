<?php
/**
 * This file is part of the TSantos Serializer package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\TSantos\Serializer\Fixture;

/**
 * Class Address
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class Address
{
    /** @var string */
    private $street;

    /** @var string */
    private $city;

    /** @var  Coordinates */
    private $coordinates;

    /**
     * @return string
     */
    public function getStreet(): string
    {
        return $this->street;
    }

    /**
     * @param string $street
     * @return Address
     */
    public function setStreet(string $street): Address
    {
        $this->street = $street;
        return $this;
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @param string $city
     * @return Address
     */
    public function setCity(string $city): Address
    {
        $this->city = $city;
        return $this;
    }

    /**
     * @return Coordinates
     */
    public function getCoordinates(): ?Coordinates
    {
        return $this->coordinates;
    }

    /**
     * @param Coordinates $coordinates
     * @return Address
     */
    public function setCoordinates(Coordinates $coordinates): Address
    {
        $this->coordinates = $coordinates;
        return $this;
    }

    public function getPoint(): string
    {
        return $this->coordinates->getX() . ',' . $this->coordinates->getY();
    }
}
