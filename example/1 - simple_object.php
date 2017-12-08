<?php
/**
 * This file is part of the TSantos Serializer package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$serializer = require 'setup.php';

use Tests\TSantos\Serializer\Fixture\Person;

$person = new Person(1, 'Tales Santos', true);
$person->setId(10);
$person->setName('Tales');
$person->setLastName('Santos');
$person->setMarried(true);

$json = $serializer->serialize($person);

echo $json;
