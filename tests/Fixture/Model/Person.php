<?php

declare(strict_types=1);
/**
 * This file is part of the TSantos Serializer package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\TSantos\Serializer\Fixture\Model;

use TSantos\Serializer\Mapping as Serializer;

/**
 * Class Person.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 * @Serializer\BaseClass("Tests\TSantos\Serializer\AbstractSerializerClass")
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
     * @Serializer\Groups({"api"})
     */
    private $name;

    /**
     * @var string
     * @Serializer\Type("string")
     */
    private $lastName;

    /**
     * @var bool
     * @Serializer\Getter("isMarried")
     * @Serializer\ExposeAs("is_married")
     */
    private $married;

    /**
     * @var array
     */
    private $colors = ['red', 'blue', 'white'];

    /**
     * @var \DateTimeInterface
     * @Serializer\Type("DateTime")
     * @Serializer\Options({"format":"d/m/Y"})
     */
    private $birthday;

    /**
     * @var Address
     * @Serializer\ReadOnly
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
     * @var string
     */
    private $club;

    /**
     * Person constructor.
     *
     * @param int    $id
     * @param string $name
     * @param bool   $married
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
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
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
     *
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
     * @Serializer\Type("string")
     * @Serializer\ExposeAs("full_name")
     * @Serializer\Groups({"api"})
     */
    public function getFullName(): string
    {
        return \trim($this->name.' '.$this->lastName);
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
     *
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
     * @Serializer\VirtualProperty()
     *
     * @return string
     */
    public function getFormattedAddress(): string
    {
        if (null === $this->address) {
            return '';
        }

        return $this->address->getStreet().' '.$this->address->getCity();
    }

    /**
     * @param Address $address
     *
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
     *
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
     *
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
     *
     * @return Person
     */
    public function setBirthday(\DateTimeInterface $birthday): Person
    {
        $this->birthday = $birthday;

        return $this;
    }

    /**
     * @param string $club
     */
    public function setClub(string $club): void
    {
        $this->club = $club;
    }
}
