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

    public function getJsonResponse(): JsonResponse
    {
        return new JsonResponse(['message' => $this->message], $this->status);
    }
}
