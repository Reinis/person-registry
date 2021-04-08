<?php

namespace PersonRegistry\Repositories;

use DateTime;
use Exception;
use InvalidArgumentException;
use PDO;
use PDOException;
use PersonRegistry\Config;
use PersonRegistry\Entities\Token;

class MySQLTokenRepository implements TokenRepository
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

    public function getTokenByNationalId(string $nid): Token
    {
        $sql = "select * from `tokens` where nid = ?;";

        return $this->fetch($sql, $nid);
    }

    private function fetch(string $sql, string ...$args): Token
    {
        $statement = $this->connection->prepare($sql);
        $statement->execute($args);
        $result = $statement->fetch();

        if ($result === false) {
            throw new InvalidArgumentException("Token not found");
        }

        try {
            $expiration_time = new DateTime($result->expiration_time);
        } catch (Exception $e) {
            throw new InvalidArgumentException("No valid token found");
        }

        return new Token($result->nid, $result->token, $expiration_time, $result->id);

    }

    public function getToken(string $token): Token
    {
        $sql = "select * from `tokens` where token = ?;";

        return $this->fetch($sql, $token);
    }

    public function setToken(Token $token): void
    {
        $sql = "insert into `tokens` (nid, token, expiration_time) values (?, ?, ?);";
        $statement = $this->connection->prepare($sql);
        $statement->execute(
            [
                $token->getNationalId(),
                $token->getToken(),
                $token->getExpirationTime()->format('Y-m-d H:i:s'),
            ]
        );
    }

    public function deleteToken(string $nid): void
    {
        $sql = "delete from `tokens` where nid = ?;";
        $statement = $this->connection->prepare($sql);
        $statement->execute([$nid]);
    }
}
