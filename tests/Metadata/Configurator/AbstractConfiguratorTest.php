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

namespace Tests\TSantos\Serializer\Metadata\Configurator;

use PHPUnit\Framework\TestCase;
use TSantos\Serializer\Metadata\ClassMetadata;
use TSantos\Serializer\Metadata\ConfiguratorInterface;

/**
 * Class AbstractConfiguratorTest.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
abstract class AbstractConfiguratorTest extends TestCase
{
    /**
     * @var ConfiguratorInterface
     */
    protected $configurator;

    protected function createClassMetadata($subject): ClassMetadata
    {
        return new ClassMetadata(\get_class($subject));
    }
}
