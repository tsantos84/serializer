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

namespace Tests\TSantos\Serializer\Metadata\Driver;

use Metadata\Driver\DriverInterface;
use Metadata\Driver\FileLocator;
use TSantos\Serializer\Metadata\Driver\XmlDriver;

/**
 * Class XmlDriverTest.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class XmlDriverTest extends AbstractDriverTest
{
    public function createDriver(): DriverInterface
    {
        return new XmlDriver(new FileLocator(
            [
            'Tests\TSantos\Serializer\Fixture\Model' => __DIR__.'/../../Resources/mapping', ]
        ));
    }
}
