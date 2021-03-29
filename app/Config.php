<?php

namespace PersonRegistry;

use Dotenv\Dotenv;

class Config
{
    private const DB_DSN = 'PERSON_REGISTRY_DB_DSN';
    private const DB_USER = 'PERSON_REGISTRY_DB_USER';
    private const DB_PASSWORD = 'PERSON_REGISTRY_DB_PASSWORD';

    private const DB_DSN_VAR = 'PERSON_REGISTRY_DB_DSN_TEST';
    private const DB_USER_VAR = 'PERSON_REGISTRY_DB_USER_TEST';
    private const DB_PASSWORD_VAR = 'PERSON_REGISTRY_DB_PASSWORD_TEST';


    private string $dsn;
    private string $user;
    private string $pass;

    public function __construct(bool $testConfig = false)
    {
        if ($testConfig) {
            $this->loadTestDBConfig();
        } else {
            $this->loadDBConfig();
        }
    }

    private function loadTestDBConfig(): void
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->load();
        $dotenv->required([self::DB_DSN_VAR, self::DB_USER_VAR, self::DB_PASSWORD_VAR]);

        $this->dsn = $_ENV[self::DB_DSN_VAR];
        $this->user = $_ENV[self::DB_USER_VAR];
        $this->pass = $_ENV[self::DB_PASSWORD_VAR];
    }

    private function loadDBConfig(): void
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->load();
        $dotenv->required([self::DB_DSN, self::DB_USER, self::DB_PASSWORD]);

        $this->dsn = $_ENV[self::DB_DSN];
        $this->user = $_ENV[self::DB_USER];
        $this->pass = $_ENV[self::DB_PASSWORD];
    }

    public function getDsn(): string
    {
        return $this->dsn;
    }

    public function getDBUsername(): string
    {
        return $this->user;
    }

    public function getDBPassword(): string
    {
        return $this->pass;
    }
}
