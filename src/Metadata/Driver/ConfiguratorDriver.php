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

namespace TSantos\Serializer\Metadata\Driver;

use Metadata\Driver\DriverInterface;
use TSantos\Serializer\Metadata\ClassMetadata;
use TSantos\Serializer\Metadata\ConfiguratorInterface;

/**
 * Class ConfiguratorDriver.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 *
 * @internal
 */
final class ConfiguratorDriver implements DriverInterface
{
    /**
     * @var DriverInterface
     */
    private $driver;

    /**
     * @var ConfiguratorInterface[]
     */
    private $configurators;

    /**
     * ConfiguratorDriver constructor.
     *
     * @param DriverInterface $driver
     * @param array           $configurators
     */
    public function __construct(DriverInterface $driver, array $configurators)
    {
        $this->driver = $driver;
        $this->configurators = $configurators;
    }

    public function loadMetadataForClass(\ReflectionClass $class)
    {
        /** @var ClassMetadata $metadata */
        $metadata = $this->driver->loadMetadataForClass($class);

        if (null === $metadata) {
            return null;
        }

        foreach ($this->configurators as $configurator) {
            $configurator->configure($metadata);
        }

        return $metadata;
    }
}
