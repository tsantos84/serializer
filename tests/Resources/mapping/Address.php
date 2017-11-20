<?php

use Tests\TSantos\Serializer\Fixture\Address;
use Tests\TSantos\Serializer\Fixture\Coordinates;

return [
    Address::class => [
        'properties' => [
            'street' => [],
            'city' => [],
            'coordinates' => [
                'type' => Coordinates::class
            ]
        ]
    ]
];
