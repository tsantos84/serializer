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

namespace Tests\TSantos\Serializer;

use PHPUnit\Framework\TestCase;
use Tests\TSantos\Serializer\Fixture\Driver\TestDriver;
use TSantos\Serializer\HydratorCompiler;
use TSantos\Serializer\Metadata\ClassMetadata;
use TSantos\Serializer\Metadata\Driver\CallbackDriver;
use TSantos\Serializer\Metadata\PropertyMetadata;
use TSantos\Serializer\Metadata\VirtualPropertyMetadata;
use TSantos\Serializer\SerializerBuilder;
use TSantos\Serializer\SerializerInterface;

/**
 * Class SerializerTestCase.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
abstract class SerializerTestCase extends TestCase
{
    protected $classCacheDir = __DIR__.'/../var/hydrators';

    protected $clearCache = true;

    protected function tearDown(): void
    {
        if ($this->clearCache && \is_dir($dir = __DIR__.'/../var')) {
            \system('rm -rf '.\escapeshellarg($dir), $retval);
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
            ->setHydratorDir($this->classCacheDir)
            ->enableBuiltInNormalizers()
            ->setHydratorGenerationStrategy(HydratorCompiler::AUTOGENERATE_ALWAYS)
            ->setDebug(true);

        return $builder->build();
    }

    protected function createMapping(string $type, array $properties, array $virtualProperties = [], array $classOptions = []): array
    {
        return [
            $type => new CallbackDriver(function (\ReflectionClass $class) use ($properties, $virtualProperties, $classOptions) {
                $metadata = new ClassMetadata($class->name);

                if (isset($classOptions['discriminatorMap'])) {
                    $metadata->setDiscriminatorMap(
                        $classOptions['discriminatorMap']['field'],
                        $classOptions['discriminatorMap']['mapping']
                    );
                    unset($classOptions['discriminatorMap']);
                }

                foreach ($classOptions as $name => $option) {
                    $metadata->{$name} = $option;
                }

                foreach ($properties as $name => $options) {
                    $pm = new PropertyMetadata($class->name, $name);

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
            }),
        ];
    }
}
