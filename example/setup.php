<?php
/**
 * This file is part of the TSantos Serializer package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require __DIR__ . '/../vendor/autoload.php';

use TSantos\Serializer\SerializerBuilder;

$builder = new SerializerBuilder();

$serializer = $builder
    ->addMetadataDir('Tests\TSantos\Serializer\Fixture', __DIR__ . '/../tests/Resources/mapping')
    ->setDebug(true)
    ->build();

return $serializer;
