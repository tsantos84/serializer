<?php
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
use TSantos\Serializer\TypeGuesser;

/**
 * Class YamlDriverTest
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class YamlDriverTest extends AbstractDriverTest
{
    public function createDriver(): DriverInterface
    {
        return new YamlDriver(new FileLocator([
            'Tests\TSantos\Serializer\Fixture' => __DIR__ . '/../../Resources/mapping']
        ), new TypeGuesser());
    }
}
