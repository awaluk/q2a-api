<?php

namespace Q2aApi\Response;

use Q2aApi\Http\JsonResponse;
use Q2aApi\Http\ResponseBodyFunctionInterface;

class VoteResponse extends JsonResponse implements ResponseBodyFunctionInterface
{
    private $userVote;

    public function __construct(int $userVote)
    {
        $this->userVote = $userVote;
        parent::__construct();
    }

    public function data(): array
    {
        return [
            'userVote' => (int)$this->userVote,
        ];
    }
}
