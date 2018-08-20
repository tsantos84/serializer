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

namespace Tests\TSantos\Serializer\Serialization;

use Tests\TSantos\Serializer\Fixture\Model\Person;
use Tests\TSantos\Serializer\SerializerTestCase;
use TSantos\Serializer\Event\PostSerializationEvent;
use TSantos\Serializer\Event\PreSerializationEvent;
use TSantos\Serializer\Events;
use TSantos\Serializer\SerializerBuilder;

/**
 * Class SerializationListenerTest.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 * @runTestsInSeparateProcesses
 */
class SerializationListenerTest extends SerializerTestCase
{
    /** @test */
    public function it_can_change_the_serialized_object()
    {
        $person = new Person(10, 'Tales');

        $serializer = $this->createSerializer($this->createMapping(Person::class, [
            'id' => ['type' => 'integer'],
            'name' => [],
            'lastName' => [],
        ]));

        $expected = '{"id":10,"name":"Tales","lastName":"Santos","age":33}';

        $this->assertSame($expected, $serializer->serialize($person));
    }

    protected function createBuilder()
    {
        return (new SerializerBuilder())
            ->addListener(Events::PRE_SERIALIZATION, function (PreSerializationEvent $event) {
                /** @var Person $person */
                $person = $event->getObject();
                $person->setLastName('Santos');
            })
            ->addListener(Events::POST_SERIALIZATION, function (PostSerializationEvent $event) {
                $data = $event->getData();
                $data['age'] = 33;
                $event->setData($data);
            });
    }
}
