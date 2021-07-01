<?php

namespace Q2aApi;

class JsonResponse extends Response
{
    public function __construct(array $body, int $status = self::STATUS_OK)
    {
        parent::__construct(
            json_encode($body),
            $status,
            ['Content-Type: application/json']
        );
    }
}
