<?php

namespace TSantos\Serializer;

/**
 * Class SerializationContext
 *
 * @package Serializer
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class SerializationContext
{
    /** @var array  */
    private $groups = ['Default'];

    /** @var bool */
    private $serializeNull = false;

    /** @var integer */
    private $maxDepth;

    /** @var integer */
    private $currentDepth;

    /**
     * @var \SplObjectStorage
     */
    private $instances;

    public static function create(): SerializationContext
    {
        return new self;
    }

    /**
     * @param array $groups
     * @return SerializationContext
     */
    public function setGroups(array $groups): SerializationContext
    {
        $this->groups = $groups;
        return $this;
    }

    public function getGroups(): array
    {
        return $this->groups;
    }

    public function setSerializeNull(bool $enabled)
    {
        $this->serializeNull = $enabled;
        return $this;
    }

    public function shouldSerializeNull(): bool
    {
        return $this->serializeNull;
    }

    /**
     * @param mixed $maxDepth
     * @return SerializationContext
     */
    public function setMaxDepth($maxDepth)
    {
        $this->maxDepth = $maxDepth;
        return $this;
    }

    public function start()
    {
        if ($this->currentDepth > 0) {
            throw new \LogicException('The serialization context cannot be restarted');
        }

        $this->instances = new \SplObjectStorage();
        $this->currentDepth = 0;
    }

    public function isStarted(): bool
    {
        return null !== $this->currentDepth;
    }

    public function enter($object = null)
    {
        if (is_object($object)) {
            $this->instances->attach($object);
        }

        $this->currentDepth++;
    }

    public function release()
    {
        $this->currentDepth--;
    }

    public function isMaxDeepAchieve(): bool
    {
        // infinite depth as the maxDepth wasn't provided
        if (null === $this->maxDepth) {
            return false;
        }

        return $this->currentDepth === $this->maxDepth;
    }

    public function hasObjectProcessed($object)
    {
        return $this->instances->contains($object);
    }
}
