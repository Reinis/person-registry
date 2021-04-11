<?php

namespace PersonRegistry\Entities;

use DateTime;

class Token
{
    private ?int $id;
    private string $nid;
    private string $token;
    private DateTime $expiration_time;

    public function __construct(string $nid, string $token, DateTime $expiration_time, ?int $id = null)
    {
        $this->id = $id;
        $this->nid = $nid;
        $this->token = $token;
        $this->expiration_time = $expiration_time;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNationalId(): string
    {
        return $this->nid;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function isExpired(): bool
    {
        return $this->getExpirationTime() < new DateTime('now');
    }

    public function getExpirationTime(): DateTime
    {
        return $this->expiration_time;
    }
}
