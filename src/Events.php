<?php
/**
 * This file is part of the TSantos Serializer package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TSantos\Serializer;

/**
 * Class SerializerEvents.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
final class Events
{
    /**
     * Called before the serialization of an object.
     *
     * Listeners have the opportunity to change the state of the object before
     * the serialization.
     *
     * @Event("TSantos\Serializer\EventDispatcher\PreSerializationEvent")
     */
    const PRE_SERIALIZATION = 'serializer.pre_serialization';

    /**
     * Called after the serialization of an object.
     *
     * Listeners have the opportunity to change the array generated by de serialization.
     *
     * @Event("TSantos\Serializer\EventDispatcher\PostSerializationEvent")
     */
    const POST_SERIALIZATION = 'serializer.post_serialization';

    /**
     * Called before the deserialization of a content.
     *
     * Listeners have the opportunity to change the array generated by the deserialization.
     *
     * @Event("TSantos\Serializer\EventDispatcher\PreDeserializationEvent")
     */
    const PRE_DESERIALIZATION = 'serializer.pre_deserialization';

    /**
     * Called after the deserialization of a content.
     *
     * Listeners have the opportunity to do some validations after the deserialization finishes.
     *
     * @Event("TSantos\Serializer\EventDispatcher\PostDeserializationEvent")
     */
    const POST_DESERIALIZATION = 'serializer.post_deserialization';
}
