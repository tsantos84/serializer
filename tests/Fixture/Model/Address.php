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
 * Class Address.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class Address
{
    /** @var string */
    private $street;

    /** @var string */
    private $city;

    /** @var Coordinates */
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
     *
     * @return Address
     */
    public function setStreet(string $street): self
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
     *
     * @return Address
     */
    public function setCity(string $city): self
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
     *
     * @return Address
     */
    public function setCoordinates(Coordinates $coordinates): self
    {
        $this->coordinates = $coordinates;

        return $this;
    }

    public function getPoint(): string
    {
        return $this->coordinates->getX().','.$this->coordinates->getY();
    }
}
