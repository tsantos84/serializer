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
 * Class Employee.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
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
     *
     * @return Employee
     */
    public function setPosition(string $position): Employee
    {
        $this->position = $position;

        return $this;
    }
}
