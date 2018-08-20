<?php
/**
 * This file is part of the TSantos Serializer package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\TSantos\Serializer\Fixture\Model\Inheritance;

/**
 * Class Airplane
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class Airplane extends AbstractVehicle
{
    /**
     * @var int
     */
    private $turbines;

    /**
     * @return int
     */
    public function getTurbines(): int
    {
        return $this->turbines;
    }

    /**
     * @param int $turbines
     */
    public function setTurbines(int $turbines): void
    {
        $this->turbines = $turbines;
    }
}
