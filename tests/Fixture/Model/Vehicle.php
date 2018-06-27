<?php

declare(strict_types=1);
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
 * Class Veihcle.
 *
 * @author Tales Santos <tales.maxmilhas@gmail.com>
 */
class Vehicle implements \JsonSerializable
{
    /** @var string */
    private $color;

    /** @var int */
    private $ports;

    /**
     * Veihcle constructor.
     *
     * @param string $color
     * @param int    $ports
     */
    public function __construct(string $color, int $ports)
    {
        $this->color = $color;
        $this->ports = $ports;
    }

    /**
     * @return string
     */
    public function getColor(): string
    {
        return $this->color;
    }

    /**
     * @param string $color
     *
     * @return Vehicle
     */
    public function setColor(string $color): Vehicle
    {
        $this->color = $color;

        return $this;
    }

    /**
     * @return int
     */
    public function getPorts(): int
    {
        return $this->ports;
    }

    /**
     * @param int $ports
     *
     * @return Vehicle
     */
    public function setPorts(int $ports): Vehicle
    {
        $this->ports = $ports;

        return $this;
    }

    public function jsonSerialize()
    {
        return [
            'color' => $this->color,
            'ports' => $this->ports,
            'owner' => 'Tales',
            'tires' => [
                'FL' => 'good',
                'FR' => 'medium',
                'BL' => 'good',
                'BR' => 'bad',
            ],
        ];
    }
}
