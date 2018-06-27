<?php
/**
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
 * Class EventDispatcherInterface.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
interface EventDispatcherInterface
{
    /**
     * @param string      $eventName
     * @param Event|null  $event
     * @param string|null $type
     *
     * @return Event
     */
    public function dispatch(string $eventName, Event $event, string $type = null): Event;

    /**
     * @param string      $eventName
     * @param callable    $listener
     * @param int         $priority
     * @param string|null $type
     *
     * @return mixed
     */
    public function addListener(string $eventName, callable $listener, int $priority = 0, string $type = null);

    /**
     * @param EventSubscriberInterface $subscriber
     *
     * @return mixed
     */
    public function addSubscriber(EventSubscriberInterface $subscriber);
}
