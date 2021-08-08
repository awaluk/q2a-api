<?php

namespace Q2aApi\Exception;

use Q2aApi\Http\Response;

class BadRequestHttpException extends HttpException
{
    public function __construct()
    {
        parent::__construct(qa_lang('q2a_api/response_bad_request'), Response::STATUS_BAD_REQUEST);
    }
}
