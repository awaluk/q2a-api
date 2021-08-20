<?php

namespace Q2aApi\Controller;

use Q2aApi\Base\AbstractController;
use Q2aApi\Http\Request;
use Q2aApi\Http\Response;
use Q2aApi\Response\QuestionsListResponse;
use Q2aApi\Service\Post\QuestionsListService;

class QuestionsController extends AbstractController
{
    private $service;

    public function __construct(Request $request)
    {
        $this->service = new QuestionsListService();

        parent::__construct($request);
    }

    public function list(): Response
    {
        list($questions, $paginator) = $this->service->getList($this->request);
        $favourites = qa_get_favorite_non_qs_map();

        return new QuestionsListResponse($questions, $favourites, $paginator);
    }

    public function home(): Response
    {
        $questions = $this->service->getHomeList();
        $favourites = qa_get_favorite_non_qs_map();

        return new QuestionsListResponse($questions, $favourites);
    }

    public function activity(): Response
    {
        $questions = $this->service->getActivityList();
        $favourites = qa_get_favorite_non_qs_map();

        return new QuestionsListResponse($questions, $favourites);
    }
}
