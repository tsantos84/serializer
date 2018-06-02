<?php
/**
 * This file is part of the TSantos Serializer package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\TSantos\Serializer;

use PHPUnit\Framework\TestCase;
use Tests\TSantos\Serializer\Fixture\Driver\TestDriver;
use TSantos\Serializer\Metadata\ClassMetadata;
use TSantos\Serializer\Metadata\Driver\CallbackDriver;
use TSantos\Serializer\Metadata\PropertyMetadata;
use TSantos\Serializer\Metadata\VirtualPropertyMetadata;
use TSantos\Serializer\SerializerBuilder;
use TSantos\Serializer\SerializerInterface;

/**
 * Class SerializerTestCase
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
abstract class SerializerTestCase extends TestCase
{
    protected $classCacheDir = __DIR__ . '/../var/classes';

    protected $clearCache = true;

    protected function tearDown()
    {
        if ($this->clearCache && is_dir($dir = __DIR__ . '/../var')) {
            system('rm -rf ' . escapeshellarg($dir), $retval);
        }
    }

    /**
     * @return SerializerBuilder
     */
    protected function createBuilder()
    {
        return new SerializerBuilder();
    }

    protected function createSerializer(array $mapping = []): SerializerInterface
    {
        $builder = $this->createBuilder();

        $builder
            ->setMetadataDriver(new TestDriver($mapping))
            ->setSerializerClassDir($this->classCacheDir)
            ->enableBuiltInNormalizers()
            ->setDebug(true);

        return $builder->build();
    }

    protected function createMapping(string $type, array $properties, array $virtualProperties = []): array
    {
        return [
            $type => new CallbackDriver(function (\ReflectionClass $class) use ($properties, $virtualProperties) {
                $metadata = new ClassMetadata($class->name);
                foreach ($properties as $name => $options) {
                    $pm = new PropertyMetadata($class->name, $name);

                    if (!isset($options['getter']) && $class->hasMethod($method = 'get' . ucfirst($name))) {
                        $options['getter'] = $method;
                        $options['getterRef'] = new \ReflectionMethod($class->name, $method);
                    }

                    if (!isset($options['setter']) && $class->hasMethod($method = 'set' . ucfirst($name))) {
                        $options['setter'] = $method;
                        $options['setterRef'] = new \ReflectionMethod($class->name, $method);
                    }

                    foreach ($options as $k => $v) {
                        $pm->{$k} = $v;
                    }

                    $metadata->addPropertyMetadata($pm);
                }

                foreach ($virtualProperties as $name => $options) {
                    $m = new VirtualPropertyMetadata($class->name, $name);
                    foreach ($options as $k => $v) {
                        $m->{$k} = $v;
                    }
                    $metadata->addMethodMetadata($m);
                }
                return $metadata;
            })
        ];
    }
}
