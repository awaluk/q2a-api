<?php

namespace Q2aApi\Response;

use Q2aApi\Base\Paginator;
use Q2aApi\Dto\QuestionDto;
use Q2aApi\Http\JsonResponse;
use Q2aApi\Http\ResponseBodyFunctionInterface;

class QuestionsListResponse extends JsonResponse implements ResponseBodyFunctionInterface
{
    private $paginator;
    private $questions;
    private $favourites;

    public function __construct(array $questions, array $favourites = [], Paginator $paginator = null)
    {
        $this->questions = $questions;
        $this->favourites = $favourites;
        $this->paginator = $paginator;

        parent::__construct();
    }

    public function data(): array
    {
        $data = [];
        foreach ($this->questions as $question) {
            $data[] = (new QuestionResponse(new QuestionDto($question), $this->favourites))->data();
        }

        if ($this->paginator === null) {
            return $data;
        }

        return [
            'data' => $data,
            'pagination' => [
                'itemsCount' => $this->paginator->getItemsCount(),
                'perPage' => $this->paginator->getPerPage(),
                'currentPage' => $this->paginator->getCurrentPage(),
                'firstPage' => $this->paginator->getFirstPage(),
                'lastPage' => $this->paginator->getLastPage(),
                'previousPage' => $this->paginator->getPreviousPage(),
                'nextPage' => $this->paginator->getNextPage(),
            ]
        ];
    }
}
