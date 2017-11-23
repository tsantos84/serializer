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

use Tests\TSantos\Serializer\Fixture\Address;
use Tests\TSantos\Serializer\Fixture\Coordinates;
use Tests\TSantos\Serializer\Fixture\Person;

$person = new Person(1,'Tales Santos', true);

$address = new Address();
$address->setCity('Belo Horizonte');
$address->setStreet('Afonso Pena');
$address->setCoordinates(new Coordinates(10.5, 20.9));
$person->setAddress($address);

$json = $serializer->serialize($person, 'json');

echo $json;
