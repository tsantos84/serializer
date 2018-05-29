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
 * Class AbstractContext
 * @package TSantos\Serializer
 */
abstract class AbstractContext
{
    /** @var array */
    private $groups = ['Default' => true];

    /** @var integer */
    private $maxDepth;

    /** @var integer */
    private $currentDepth;

    /**
     * @return self
     */
    public static function create(): self
    {
        return new static();
    }

    /**
     * @param array $groups
     * @return self
     */
    public function setGroups(array $groups): self
    {
        $this->groups = array_flip($groups);
        return $this;
    }

    public function getGroups(): array
    {
        return $this->groups;
    }

    /**
     * @param $maxDepth
     * @return self
     */
    public function setMaxDepth($maxDepth): self
    {
        $this->maxDepth = $maxDepth;
        return $this;
    }

    public function start()
    {
        if ($this->currentDepth > 0) {
            throw new \LogicException('The context cannot be restarted');
        }

        $this->currentDepth = 0;
    }

    public function isStarted(): bool
    {
        return null !== $this->currentDepth;
    }

    public function enter($object = null)
    {
        $this->currentDepth++;
    }

    public function left()
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

    /**
     * @return int
     */
    public function getCurrentDepth(): int
    {
        return $this->currentDepth;
    }
}
