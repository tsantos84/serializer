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

/**
 * Class SerializerEvents
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
final class SerializerEvents
{
    const PRE_SERIALIZATION = 'serializer.pre_serialization';
    const POST_SERIALIZATION = 'serializer.post_serialization';
    const PRE_DESERIALIZATION = 'serializer.post_deserialization';
    const POST_DESERIALIZATION = 'serializer.post_deserialization';
}
