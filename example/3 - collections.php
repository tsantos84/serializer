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

$persons = [];

for ($i=1; $i<=10; $i++) {
    $person = new Person(1, 'Tales Santos', true);
    $person->setId(10);
    $person->setName('Tales');
    $person->setMarried(true);
    $persons[] = $person;
}

$json = $serializer->serialize($persons, 'json');

echo $json;
