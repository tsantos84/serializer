<?php

declare(strict_types=1);
/**
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
use TSantos\Serializer\Event\PostDeserializationEvent;
use TSantos\Serializer\Event\PreDeserializationEvent;
use TSantos\Serializer\Events;
use TSantos\Serializer\SerializerBuilder;

/**
 * Class DeserializationListenerTest.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class DeserializationListenerTest extends SerializerTestCase
{
    /** @test */
    public function it_can_change_the_serialized_object()
    {
        $content = '{"name":"Tales","lastName":"Santos","married":true}';

        $serializer = $this->createSerializer($this->createMapping(Person::class, [
            'id' => ['type' => 'integer'],
            'name' => [],
            'lastName' => [],
            'married' => ['type' => 'boolean', 'getter' => 'isMarried'],
        ]));

        /** @var Person $person */
        $person = $serializer->deserialize($content, Person::class);

        $this->assertEquals('Tales', $person->getName());
        $this->assertEquals('Santos', $person->getLastName());
        $this->assertTrue($person->isMarried());
    }

    protected function createBuilder()
    {
        return (new SerializerBuilder())
            ->addListener(Events::PRE_DESERIALIZATION, function (PreDeserializationEvent $event) {
                $data = $event->getData();
                $data['lastName'] = 'Santos';
                $event->setData($data);
            })
            ->addListener(Events::POST_DESERIALIZATION, function (PostDeserializationEvent $event) {
                /** @var Person $person */
                $person = $event->getObject();
                $person->setMarried(true);
            });
    }
}
