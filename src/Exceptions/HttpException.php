<?php

namespace Q2aApi;

class HttpException extends \Exception
{
    private $status;

    public function __construct(string $message, int $status)
    {
        $this->status = $status;

        parent::__construct($message);
    }

    public function getJsonResponse()
    {
        return new Response(
            json_encode(['message' => $this->message]),
            $this->status,
            ['Content-Type: application/json']
        );
    }
}
