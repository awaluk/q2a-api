<?php

namespace Q2aApi;

abstract class AbstractController
{
    public function json(array $data, int $status = Response::STATUS_OK): Response
    {
        return new Response(
            json_encode($data),
            $status,
            ['Content-Type: application/json']
        );
    }
}
