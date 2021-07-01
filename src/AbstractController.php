<?php

namespace Q2aApi;

abstract class AbstractController
{
    public function json(array $data, int $status = Response::STATUS_OK): Response
    {
        return new JsonResponse($data, $status);
    }
}
