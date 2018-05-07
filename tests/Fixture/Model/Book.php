<?php
/**
 * This file is part of the TSantos Serializer package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\TSantos\Serializer\Fixture\Model;

use TSantos\Serializer\Normalizer\IdentifiableInterface;

/**
 * Class Book
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class Book implements IdentifiableInterface
{
    /** @var integer */
    private $id;

    /** @var string */
    private $name;

    /**
     * Book constructor.
     * @param int $id
     * @param string $name
     */
    public function __construct(int $id = null, string $name = null)
    {
        $this->id = $id;
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Book
     */
    public function setId(int $id): Book
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Book
     */
    public function setName(string $name): Book
    {
        $this->name = $name;
        return $this;
    }
}
