<?php

namespace PersonRegistry\Entities\Collections;

use ArrayIterator;
use Countable;
use InvalidArgumentException;
use IteratorAggregate;
use PersonRegistry\Entities\Person;

class People implements IteratorAggregate, Countable
{
    /**
     * @var Person[]
     */
    private array $people = [];

    public function __construct(Person ...$people)
    {
        foreach ($people as $person) {
            $this->addPerson($person);
        }
    }

    public function getPersonById(int $id): Person
    {
        return $this->people[$id];
    }

    public function addPerson(Person $person): void
    {
        if ($this->hasPerson($person->getNationalId())) {
            throw new InvalidArgumentException("Person with NID '{$person->getNationalId()}' already exists");
        }

        $id = $person->getId();

        if (isset($this->people[$id])) {
            throw new InvalidArgumentException("Duplicate ID {$id}");
        }

        $this->people[$id] = $person;
    }

    public function hasPerson(string $nationalId): bool
    {
        foreach ($this->people as $person) {
            if ($person->getNationalId() === $nationalId) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return ArrayIterator|Person[]
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->people);
    }

    public function count(): int
    {
        return count($this->people);
    }
}
