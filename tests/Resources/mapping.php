<?php

use Tests\Serializer\Fixture\Person;

return [
    Person::class => [
        'properties' => [
            'id' => [
                'type' => 'integer'
            ],
            'name' => [
                'type' => 'string',
                'exposeAs' => 'nome'
            ],
            'lastName' => [
                'type' => 'string',
                'exposeAs' => 'last_name'
            ],
            'married' => [
                'type' => 'boolean',
                'getter' => 'isMarried'
            ],
            'colors' => [
                'type' => 'array'
            ]
        ]
    ]
];
