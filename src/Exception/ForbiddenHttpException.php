<?php

namespace Q2aApi\Exception;

use Q2aApi\Http\Response;

class ForbiddenHttpException extends HttpException
{
    public function __construct()
    {
        parent::__construct(qa_lang('q2a_api/response_forbidden'), Response::STATUS_FORBIDDEN);
    }
}
