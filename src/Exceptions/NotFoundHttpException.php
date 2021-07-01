<?php

namespace Q2aApi;

class NotFoundHttpException extends HttpException
{
    public function __construct()
    {
        parent::__construct(qa_lang('q2a_api/response_not_found'), Response::STATUS_NOT_FOUND);
    }
}
