<?php

namespace Q2aApi\Http;

class JsonResponse extends Response
{
    public function __construct(array $body = [], int $status = self::STATUS_OK)
    {
        if ($this instanceof ResponseBodyFunctionInterface) {
            $body = $this->data();
        }

        parent::__construct(
            json_encode($body),
            $status,
            ['Content-Type: application/json']
        );
    }
}
