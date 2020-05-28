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

namespace TSantos\Serializer\EventDispatcher;

use TSantos\Serializer\Event\Event;

/**
 * Class EventDispatcher.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class EventDispatcher implements EventDispatcherInterface
{
    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * EventDispatcher constructor.
     *
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher
     */
    public function __construct(\Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher = null)
    {
        $this->dispatcher = $dispatcher ?? new \Symfony\Component\EventDispatcher\EventDispatcher();
    }

    public function dispatch(string $eventName, Event $event, string $type = null): Event
    {
        if (null !== $type && $this->dispatcher->hasListeners($typeEventName = $eventName.'.'.$type)) {
            $this->dispatcher->dispatch($event, $eventName);
        }

        if ($this->dispatcher->hasListeners($eventName)) {
            $this->dispatcher->dispatch($event, $eventName);
        }

        return $event;
    }

    public function addListener(string $eventName, callable $listener, int $priority = 0, string $type = null)
    {
        if (null !== $type) {
            $eventName .= '.'.$type;
        }

        $this->dispatcher->addListener($eventName, $listener, $priority);
    }

    public function addSubscriber(EventSubscriberInterface $subscriber)
    {
        foreach ($subscriber->getListeners() as $event => $params) {
            if (\is_string($params)) {
                $this->addListener($event, [$subscriber, $params]);
            } elseif (\is_array($params)) {
                $this->addListener(
                    $event,
                    [$subscriber, $params['method']],
                    $params['priority'] ?? 0,
                    $params['type'] ?? null
                );
            }
        }
    }
}
