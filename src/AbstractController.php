<?php

namespace Q2aApi;

abstract class AbstractController
{
    public function json(array $data, int $status = Response::STATUS_OK): Response
    {
        return new JsonResponse($data, $status);
    }

    public function errors(
        array $fields = [],
        string $message = null,
        int $status = Response::STATUS_BAD_REQUEST
    ): Response {
        $data = [];
        if (!empty($message)) {
            $data['message'] = $message;
        }
        if (!empty($fields)) {
            $data['fields'] = $fields;
        }

        return new JsonResponse($data, $status);
    }
}
