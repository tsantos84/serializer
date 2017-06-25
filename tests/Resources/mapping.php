<?php

use Tests\TSantos\Serializer\Fixture\Address;
use Tests\TSantos\Serializer\Fixture\Coordinates;
use Tests\TSantos\Serializer\Fixture\Person;

return [
    Person::class => [
        'properties' => [
            'id' => [
                'groups' => ['web']
            ],
            'name' => [
                'exposeAs' => 'nome'
            ],
            'lastName' => [
                'exposeAs' => 'last_name',
                'groups' => ['web']
            ],
            'married' => [
                'getter' => 'isMarried'
            ],
            'colors' => [
                'groups' => ['mobile']
            ],
            'address' => [
                'type' => Address::class
            ]
        ],
        'virtual_properties' => [
            'fullName' => [
                'exposeAs' => 'full_name'
            ]
        ]
    ],
    Address::class => [
        'properties' => [
            'street' => [],
            'city' => [],
            'coordinates' => [
                'type' => Coordinates::class
            ]
        ]
    ],
    Coordinates::class => [
        'properties' => [
            'x' => [],
            'y' => []
        ]
    ]
];
