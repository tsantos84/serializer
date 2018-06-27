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
 * Class Coordinates.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class Coordinates
{
    /** @var float */
    private $x;

    /** @var float */
    private $y;

    /**
     * Coordinates constructor.
     *
     * @param float $x
     * @param float $y
     */
    public function __construct(float $x, float $y)
    {
        $this->x = $x;
        $this->y = $y;
    }

    /**
     * @return float
     */
    public function getX(): float
    {
        return $this->x;
    }

    /**
     * @param float $x
     *
     * @return Coordinates
     */
    public function setX(float $x): Coordinates
    {
        $this->x = $x;

        return $this;
    }

    /**
     * @return float
     */
    public function getY(): float
    {
        return $this->y;
    }

    /**
     * @param float $y
     *
     * @return Coordinates
     */
    public function setY(float $y): Coordinates
    {
        $this->y = $y;

        return $this;
    }
}
