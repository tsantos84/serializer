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

namespace Tests\TSantos\Serializer\Fixture;

use TSantos\Serializer\Event\Event;
use TSantos\Serializer\EventDispatcher\EventDispatcherInterface;
use TSantos\Serializer\EventDispatcher\EventSubscriberInterface;

/**
 * Class DummyEventDispatcher.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class DummyEventDispatcher implements EventDispatcherInterface
{
    private $listeners = [];
    private $subscribers = [];

    public function dispatch(string $eventName, Event $event, string $type = null): Event
    {
        return $event;
    }

    public function addListener(string $eventName, callable $listener, int $priority = 0, string $type = null)
    {
        $this->listeners[] = [
            'eventName' => $eventName,
            'listener' => $listener,
            'priority' => $priority,
            'type' => $type,
        ];
    }

    public function addSubscriber(EventSubscriberInterface $subscriber)
    {
        $this->subscribers[] = $subscriber;
    }

    public function getListeners(): array
    {
        return $this->listeners;
    }

    public function getSubscribers(): array
    {
        return $this->subscribers;
    }
}
