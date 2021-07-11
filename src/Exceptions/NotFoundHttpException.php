<?php

namespace Q2aApi\Exceptions;

use Q2aApi\Http\Response;

class NotFoundHttpException extends HttpException
{
    public function __construct()
    {
        parent::__construct(qa_lang('q2a_api/response_not_found'), Response::STATUS_NOT_FOUND);
    }
}
