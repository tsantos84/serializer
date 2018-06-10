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
use TSantos\Serializer\Exception\CircularReferenceException;

/**
 * Class SerializationContext
 *
 * @package Serializer
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class SerializationContext extends AbstractContext
{
    /** @var bool */
    private $serializeNull = false;

    private $graph = [];

    private $currentObject;

    public function setSerializeNull(bool $enabled)
    {
        $this->serializeNull = $enabled;
        return $this;
    }

    public function shouldSerializeNull(): bool
    {
        return $this->serializeNull;
    }

    public function enter($object = null)
    {
        if (!is_object($object)) {
            parent::enter($object);
            return;
        }

        if (null === $this->currentObject) {
            $this->currentObject = $object;
            parent::enter($object);
            return;
        }

        $from = spl_object_id($this->currentObject);
        $to = spl_object_id($object);

        if (!isset($this->graph[$from])) {
            $this->graph[$from] = [$to => true];
            parent::enter($object);
            return;
        }

        $fromGraph = $this->graph[$from];

        if (isset($fromGraph[$to])) {
            throw new CircularReferenceException(
                'A circular reference was detected'
            );
        }

        parent::enter($object);
    }
}
