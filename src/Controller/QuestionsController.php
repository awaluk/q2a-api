<?php

namespace Q2aApi\Controller;

use Q2aApi\Base\AbstractController;
use Q2aApi\Http\Request;
use Q2aApi\Http\Response;
use Q2aApi\Response\QuestionsListResponse;
use Q2aApi\Service\QuestionsListService;

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
        $userId = qa_get_logged_in_userid();
        list($questions1, $questions2) = qa_db_select_with_pending(
            qa_db_qs_selectspec($userId, 'created', 0, [], null, false, false, qa_opt_if_loaded('page_size_activity')),
            qa_db_recent_a_qs_selectspec($userId, 0, [])
        );
        $questions = qa_any_sort_and_dedupe(array_merge($questions1, $questions2));
        $favourites = qa_get_favorite_non_qs_map();

        return new QuestionsListResponse($questions, $favourites);
    }

    public function activity(): Response
    {
        $userId = qa_get_logged_in_userid();
        list($questions1, $questions2, $questions3, $questions4) = qa_db_select_with_pending(
            qa_db_qs_selectspec($userId, 'created', 0, [], null, false, false, qa_opt_if_loaded('page_size_activity')),
            qa_db_recent_a_qs_selectspec($userId, 0, []),
            qa_db_recent_c_qs_selectspec($userId, 0, []),
            qa_db_recent_edit_qs_selectspec($userId, 0, [])
        );

        $questions = qa_any_sort_and_dedupe(array_merge($questions1, $questions2, $questions3, $questions4));
        $favourites = qa_get_favorite_non_qs_map();

        return new QuestionsListResponse($questions, $favourites);
    }
}
