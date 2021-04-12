<?php

namespace PersonRegistry\Services;

use DateTime;
use Exception;
use InvalidArgumentException;
use PersonRegistry\Entities\Token;
use PersonRegistry\Repositories\PersonRepository;
use PersonRegistry\Repositories\TokenRepository;
use RuntimeException;

class TokenService
{
    private PersonRepository $personRepository;
    private TokenRepository $tokenRepository;

    public function __construct(PersonRepository $personRepository, TokenRepository $tokenRepository)
    {
        $this->personRepository = $personRepository;
        $this->tokenRepository = $tokenRepository;
    }

    public function getToken(string $token): ?Token
    {
        try {
            $tokenObj = $this->tokenRepository->getToken($token);
        } catch (InvalidArgumentException $e) {
            return null;
        }

        if ($tokenObj->isExpired()) {
            return null;
        }

        return $tokenObj;
    }

    public function getTokenByNationalId($nid): ?Token
    {
        try {
            $person = $this->personRepository->getPersonByNId($nid);
            $token = $this->tokenRepository->getTokenByNationalId($person->getNationalId());
        } catch (InvalidArgumentException $e) {
            return null;
        }

        if ($token->isExpired()) {
            return null;
        }

        return $token;
    }

    public function setToken(string $nid): void
    {
        $person = $this->personRepository->getPersonByNId($nid);

        $nid = $person->getNationalId();

        // Delete any previously set tokens
        $this->tokenRepository->deleteToken($nid);

        $time = new DateTime();
        try {
            $token = sha1($nid . $time->format('Y-m-d H:i:s P') . random_bytes(16));
        } catch (Exception $e) {
            throw new RuntimeException("Failed to generate a token");
        }
        $expiration_time = $time->modify('+15min');

        $this->tokenRepository->setToken(new Token($nid, $token, $expiration_time));
    }

    public function deleteToken(string $nid): void
    {
        $this->tokenRepository->deleteToken($nid);
    }
}
