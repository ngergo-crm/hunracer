<?php

namespace App\Message;

class WorkoutDetailForDownload
{
    private $wishlistId;
    private $endpoint;
    private $token;
    private $isTest;

    public function __construct(int $wishlistId, string $endpoint, array $token, bool $isTest)
    {
        $this->wishlistId = $wishlistId;
        $this->endpoint = $endpoint;
        $this->token = $token;
        $this->isTest = $isTest;
    }

    public function getWishlistId(): int
    {
        return $this->wishlistId;
    }

    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    public function getToken(): array
    {
        return $this->token;
    }

    public function getIsTest(): bool
    {
        return $this->isTest;
    }

}
