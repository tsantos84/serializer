<?php

namespace Tests\Serializer\Fixture;

/**
 * Class Coordinates
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
     * @return Coordinates
     */
    public function setY(float $y): Coordinates
    {
        $this->y = $y;
        return $this;
    }
}
