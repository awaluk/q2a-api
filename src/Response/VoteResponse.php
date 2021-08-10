<?php

namespace Q2aApi\Response;

use Q2aApi\Http\JsonResponse;
use Q2aApi\Http\ResponseBodyFunctionInterface;

class VoteResponse extends JsonResponse implements ResponseBodyFunctionInterface
{
    private $userVote;
    private $votes;

    public function __construct(int $userVote, int $votes)
    {
        $this->userVote = $userVote;
        $this->votes = $votes;
        parent::__construct();
    }

    public function data(): array
    {
        return [
            'userVote' => (int)$this->userVote,
            'votes' => (int)$this->votes
        ];
    }
}
