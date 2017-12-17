<?php
/**
 * This file is part of the TSantos Serializer package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\TSantos\Serializer\Serialization;

use Tests\TSantos\Serializer\Fixture\Person;
use Tests\TSantos\Serializer\SerializerTestCase;
use TSantos\Serializer\EventDispatcher\Event\PostSerializationEvent;
use TSantos\Serializer\EventDispatcher\Event\PreSerializationEvent;
use TSantos\Serializer\EventDispatcher\SerializerEvents;
use TSantos\Serializer\SerializerBuilder;

/**
 * Class SerializationListenerTest
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class SerializationListenerTest extends SerializerTestCase
{
    protected $clearCache = false;

    /** @test */
    public function it_can_change_the_serialized_object()
    {
        $person = new Person(10);

        $serializer = $this->createSerializer($this->createMapping(Person::class, [
            'id' => ['type' => 'integer'],
            'name' => []
        ]));

        $expected = '{"id":10,"name":"Tales Santos","age":33}';

        $this->assertEquals($expected, $serializer->serialize($person));
    }

    protected function createBuilder()
    {
        return (new SerializerBuilder())
            ->addListener(SerializerEvents::PRE_SERIALIZATION, function (PreSerializationEvent $event) {
                /** @var Person $person */
                $person = $event->getObject();
                $person->setName('Tales Santos');
            })
            ->addListener(SerializerEvents::POST_SERIALIZATION, function (PostSerializationEvent $event) {
                $data = $event->getData();
                $data['age'] = 33;
                $event->setData($data);
            });
    }
}
