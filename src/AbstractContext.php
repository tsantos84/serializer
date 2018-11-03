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

namespace TSantos\Serializer;

/**
 * Class AbstractContext.
 */
abstract class AbstractContext
{
    /**
     * @var string
     */
    private $id;

    /** @var array */
    private $groups;

    /** @var int */
    private $maxDepth;

    /** @var int */
    private $currentDepth;

    /**
     * AbstractContext constructor.
     */
    public function __construct()
    {
        $this->id = \spl_object_hash($this);
    }

    /**
     * @return self
     */
    public static function create(): self
    {
        return new static();
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Set the property groups.
     *
     * Although you can set the groups in the context, you need to enable this feature in you serializer instance
     * to get this feature working on you project.
     *
     * @see https://tsantos-serializer.readthedocs.io/en/latest/usage.html#property-grouping
     *
     * @param array $groups
     *
     * @return self
     */
    public function setGroups(array $groups): self
    {
        $this->groups = \array_flip($groups);

        return $this;
    }

    public function getGroups(): ?array
    {
        return $this->groups;
    }

    /**
     * @param $maxDepth
     *
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

    public function enter()
    {
        ++$this->currentDepth;
    }

    public function leave()
    {
        --$this->currentDepth;
    }

    /**
     * @return int
     */
    public function getCurrentDepth(): int
    {
        return $this->currentDepth;
    }
}
