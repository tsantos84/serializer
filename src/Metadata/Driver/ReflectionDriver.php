<?php
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
use TSantos\Serializer\Metadata\PropertyMetadata;

/**
 * Class ReflectionDriver
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 * @internal
 */
class ReflectionDriver implements DriverInterface
{
    public function loadMetadataForClass(\ReflectionClass $class)
    {
        $metadata = new ClassMetadata($class->name);

        foreach ($class->getProperties() as $property) {
            $metadata->addPropertyMetadata(new PropertyMetadata($class->name, $property->name));
        }

        return $metadata;
    }
}
