<?php

namespace Q2aApi\Response;

use Q2aApi\Http\JsonResponse;
use Q2aApi\Http\ResponseBodyFunctionInterface;

class AccountResponse extends JsonResponse implements ResponseBodyFunctionInterface
{
    public function data(): array
    {
        return [
            'id' => (int)qa_get_logged_in_userid(),
            'name' => qa_get_logged_in_handle(),
            'email' => qa_get_logged_in_email(),
            'level' => (int)qa_get_logged_in_level(),
            'points' => (int)qa_get_logged_in_points(),
        ];
    }
}
