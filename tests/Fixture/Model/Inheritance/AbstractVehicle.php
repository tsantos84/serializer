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

use TSantos\Serializer\Mapping as Serializer;

/**
 * Class AbstractVehicle.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 *
 * @Serializer\Discriminator(field="type", map={"car":"Tests\TSantos\Serializer\Fixture\Model\Inheritance\Car", "airplane":"Tests\TSantos\Serializer\Fixture\Model\Inheritance\Airplane"})
 */
abstract class AbstractVehicle
{
    /**
     * @var string
     */
    private $color;

    /**
     * AbstractVehicle constructor.
     *
     * @param string $color
     */
    public function __construct(string $color)
    {
        $this->color = $color;
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
     */
    public function setColor(string $color): void
    {
        $this->color = $color;
    }
}
