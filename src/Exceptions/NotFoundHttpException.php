<?php

namespace Q2aApi;

class NotFoundHttpException extends HttpException
{
    public function __construct()
    {
        parent::__construct('Not found', Response::STATUS_NOT_FOUND);
    }
}
