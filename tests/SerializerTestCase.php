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
use TSantos\Serializer\Metadata\Driver\InMemoryDriver;
use TSantos\Serializer\SerializerBuilder;
use TSantos\Serializer\SerializerInterface;
use TSantos\Serializer\TypeGuesser;

/**
 * Class SerializerTestCase
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
abstract class SerializerTestCase extends TestCase
{
    protected $classCacheDir;

    protected function setUp()
    {
        $this->classCacheDir = sys_get_temp_dir() . '/serializer/cache';
    }

    protected function tearDown()
    {
        system('rm -rf ' . escapeshellarg($this->classCacheDir), $retval);
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
            ->setMetadataDriver(new InMemoryDriver($mapping, new TypeGuesser()))
            ->setSerializerClassDir($this->classCacheDir)
            ->setDebug(true);

        return $builder->build();
    }

    protected function createMapping(string $type, array $properties, array $virtualProperties = []): array
    {
        return [
            $type => [
                'properties' => $properties,
                'virtual_properties' => $virtualProperties
            ]
        ];
    }
}
