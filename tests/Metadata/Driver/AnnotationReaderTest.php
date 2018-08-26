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

use Doctrine\Common\Annotations\AnnotationReader;
use Metadata\Driver\DriverInterface;
use TSantos\Serializer\Metadata\Driver\AnnotationDriver;

/**
 * Class AnnotationReaderTest.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class AnnotationReaderTest extends AbstractDriverTest
{
    public function createDriver(): DriverInterface
    {
        return new AnnotationDriver(new AnnotationReader());
    }
}
