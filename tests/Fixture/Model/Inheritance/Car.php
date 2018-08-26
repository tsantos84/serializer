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

namespace Tests\TSantos\Serializer\Fixture\Model\Inheritance;

/**
 * Class Car.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class Car extends AbstractVehicle
{
    /**
     * @var int
     */
    private $doors;

    public function __construct(string $color, int $doors)
    {
        parent::__construct($color);
        $this->doors = $doors;
    }

    /**
     * @return int
     */
    public function getDoors(): int
    {
        return $this->doors;
    }

    /**
     * @param int $doors
     */
    public function setDoors(int $doors): void
    {
        $this->doors = $doors;
    }
}
