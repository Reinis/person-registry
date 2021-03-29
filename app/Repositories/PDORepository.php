<?php

namespace PersonRegistry\Repositories;

use InvalidArgumentException;
use PDO;
use PDOException;
use PersonRegistry\Config;
use PersonRegistry\Entities\Collections\People;
use PersonRegistry\Entities\Person;

class PDORepository implements PersonRepository
{
    private PDO $connection;

    public function __construct(Config $config)
    {
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            $this->connection = new PDO(
                $config->getDsn(),
                $config->getDBUsername(),
                $config->getDBPassword(),
                $options
            );
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
        $statement->setFetchMode(PDO::FETCH_OBJ);
        $statement->execute($args);
        $result = $statement->fetch();

        if ($result === false) {
            throw new InvalidArgumentException($errorMessage);
        }

        return $this->getPersonInstance($result);
    }

    private function getPersonInstance(object $result): Person
    {
        $person = new Person($result->firstName, $result->lastName, $result->nationalId, $result->notes);
        $person->setId($result->id);

        return $person;
    }

    public function getPersonById(int $id): Person
    {
        $sql = "select * from `people` where id = ?;";
        $errorMessage = "Person with ID '{$id}' not found";

        return $this->run($sql, $errorMessage, $id);
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

    public function getPeople(): People
    {
        $sql = "select * from `people`;";
        $statement = $this->connection->prepare($sql);
        $statement->setFetchMode(PDO::FETCH_OBJ);
        $statement->execute();
        $result = $statement->fetchAll();

        if ($result === false) {
            throw new InvalidArgumentException("No people found in the database");
        }

        $peopleList = [];
        foreach ($result as $item) {
            $peopleList[] = $this->getPersonInstance($item);
        }

        return new People(...$peopleList);
    }
}
