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

namespace Tests\TSantos\Serializer\Metadata\Driver;

use Metadata\Driver\DriverInterface;
use Metadata\Driver\FileLocator;
use TSantos\Serializer\Metadata\Driver\YamlDriver;

/**
 * Class YamlDriverTest.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class YamlDriverTest extends AbstractDriverTest
{
    public function setUp()
    {
        parent::setUp();
        if (!\class_exists('Symfony\Component\Yaml\Yaml')) {
            $this->markTestSkipped('Skipping YamlDriver tests as symfony/yaml component is not installed');
        }
    }

    public function createDriver(): DriverInterface
    {
        return new YamlDriver(new FileLocator(
            [
            'Tests\TSantos\Serializer\Fixture\Model' => __DIR__.'/../../Resources/mapping', ]
        ));
    }
}
