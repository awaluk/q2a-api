<?php

namespace Q2aApi;

class Response
{
    const STATUS_OK = 200;

    const STATUS_UNAUTHORIZED = 401;
    const STATUS_NOT_FOUND = 404;

    private $body;
    private $headers;
    private $status;

    public function __construct(string $body, int $status = self::STATUS_OK, array $headers = [])
    {
        $this->body = $body;
        $this->status = $status;
        $this->headers = $headers;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getStatus(): int
    {
        return $this->status;
    }
}
