<?php
/**
 * This file is part of the TSantos Serializer package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\TSantos\Serializer\Fixture\Driver;

use Metadata\Driver\DriverInterface;

/**
 * Class TestDriver.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class TestDriver implements DriverInterface
{
    /**
     * @var DriverInterface[]
     */
    private $drivers;

    /**
     * TestDriver constructor.
     *
     * @param array $drivers
     */
    public function __construct(array $drivers)
    {
        $this->drivers = $drivers;
    }

    public function loadMetadataForClass(\ReflectionClass $class)
    {
        if (!isset($this->drivers[$class->getName()])) {
            return null;
        }

        $driver = $this->drivers[$class->getName()];

        return $driver->loadMetadataForClass($class);
    }
}
