<?php
/**
 * This file is part of the TSantos Serializer package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\TSantos\Serializer\Fixture;

use TSantos\Serializer\Mapping as Serializer;

/**
 * Class Person
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class Person
{
    /**
     * @var int
     * @Serializer\Type("integer")
     */
    private $id;

    /**
     * @var string
     * @Serializer\Type("string")
     */
    private $name;

    /**
     * @var string
     */
    private $lastName;

    /**
     * @var bool
     */
    private $married;

    /**
     * @var array
     */
    private $colors = ['red', 'blue', 'white'];

    /**
     * @var \DateTimeInterface
     * @Serializer\Modifier("format('d/m/Y')")
     */
    private $birthday;

    /**
     * @var Address
     */
    private $address;

    /**
     * @var Person
     * @Serializer\Type(Person::class)
     */
    private $father;

    /**
     * @var Book
     */
    private $favouriteBook;

    /**
     * Person constructor.
     * @param int $id
     * @param string $name
     * @param bool $married
     */
    public function __construct(int $id = null, string $name = null, bool $married = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->married = $married;
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
     * @return Person
     */
    public function setId(int $id): Person
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
     * @return Person
     */
    public function setName(string $name): Person
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     * @return Person
     */
    public function setLastName(string $lastName): Person
    {
        $this->lastName = $lastName;
        return $this;
    }

    /**
     * @return string
     * @Serializer\VirtualProperty
     * @Serializer\ExposeAs("full_name")
     */
    public function getFullName(): string
    {
        return trim($this->name . ' ' . $this->lastName);
    }

    /**
     * @return bool
     */
    public function isMarried(): bool
    {
        return $this->married;
    }

    /**
     * @param bool $isMarried
     * @return Person
     */
    public function setMarried(bool $isMarried): Person
    {
        $this->married = $isMarried;
        return $this;
    }

    public function getColors(): array
    {
        return $this->colors;
    }

    /**
     * @param array $colors
     */
    public function setColors(array $colors): void
    {
        $this->colors = $colors;
    }

    /**
     * @return Address
     */
    public function getAddress(): ?Address
    {
        return $this->address;
    }

    /**
     * @param Address $address
     * @return Person
     */
    public function setAddress(Address $address): Person
    {
        $this->address = $address;
        return $this;
    }

    /**
     * @return Person
     */
    public function getFather(): ?Person
    {
        return $this->father;
    }

    /**
     * @param Person $father
     * @return Person
     */
    public function setFather(Person $father): Person
    {
        $this->father = $father;
        return $this;
    }

    /**
     * @return Book
     */
    public function getFavouriteBook(): Book
    {
        return $this->favouriteBook;
    }

    /**
     * @param Book $favouriteBook
     * @return Person
     */
    public function setFavouriteBook(Book $favouriteBook): Person
    {
        $this->favouriteBook = $favouriteBook;
        return $this;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getBirthday(): ?\DateTimeInterface
    {
        return $this->birthday;
    }

    /**
     * @param \DateTimeInterface $birthday
     * @return Person
     */
    public function setBirthday(\DateTimeInterface $birthday): Person
    {
        $this->birthday = $birthday;
        return $this;
    }
}
