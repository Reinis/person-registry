<?php

namespace PersonRegistry\Repositories;

use PersonRegistry\Entities\Token;

interface TokenRepository
{
    public function getTokenByNationalId(string $nid): Token;

    public function setToken(Token $token): void;

    public function deleteToken(string $nid): void;

    public function getToken(string $token): Token;
}
