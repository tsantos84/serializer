<?php

namespace Tests\Serializer\Fixture;

class Person
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
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
    private $colors = []; // = ['red', 'blue', 'white'];

    /**
     * @var Address
     */
    private $address;

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
}
