<?php

namespace PersonRegistry\Repositories;

use InvalidArgumentException;
use PDO;
use PDOException;
use PersonRegistry\Entities\Person;

class PDORepository implements DataRepositoryInterface
{
    private PDO $connection;

    public function __construct(string $connectionString, string $user, string $password)
    {
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            $this->connection = new PDO($connectionString, $user, $password, $options);
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage(), (int)$e->getCode());
        }
    }

    public function getPersonByName(string $firstName, string $lastName): Person
    {
        $sql = "select * from `people` where firstName = ? and lastName = ?";
        $errorMessage = "Person not found: {$firstName} {$lastName}";

        return $this->run($sql, $errorMessage, $firstName, $lastName);
    }

    private function run(string $sql, string $errorMessage, string ...$args): Person
    {
        $statement = $this->connection->prepare($sql);
        $statement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, Person::class);
        $statement->execute($args);
        $person = $statement->fetch();

        if ($person === false) {
            throw new InvalidArgumentException($errorMessage);
        }

        return $person;
    }

    public function getPersonByNId(string $nationalId): Person
    {
        $sql = "select * from `people` where nationalId = ?;";
        $errorMessage = "Person with NID '{$nationalId}' not found";

        return $this->run($sql, $errorMessage, $nationalId);
    }

    public function updatePerson(Person $person): void
    {
        $sql = "update `people` set firstName = ?, lastName = ?, nationalId = ?, notes = ? where nationalId = ?";
        $statement = $this->connection->prepare($sql);
        $statement->execute(
            [
                $person->getFirstName(),
                $person->getLastName(),
                $person->getNationalId(),
                $person->getNotes(),
                $person->getNationalId(),
            ]
        );
    }

    public function createPerson(Person $person): void
    {
        $sql = "insert into `people` (firstName, lastName, nationalId, notes) values (?, ?, ?, ?);";
        $statement = $this->connection->prepare($sql);
        $statement->execute(
            [
                $person->getFirstName(),
                $person->getLastName(),
                $person->getNationalId(),
                $person->getNotes(),
            ]
        );
    }

    public function deletePerson(Person $person): void
    {
        $sql = "delete from `people` where nationalId = ?;";
        $statement = $this->connection->prepare($sql);
        $statement->execute([$person->getNationalId()]);
    }
}
