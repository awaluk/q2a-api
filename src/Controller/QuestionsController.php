<?php

namespace Q2aApi\Controller;

use Q2aApi\Base\AbstractController;
use Q2aApi\Http\Response;
use Q2aApi\Response\QuestionsListResponse;

class QuestionsController extends AbstractController
{
    public function list(): Response
    {
        $start = qa_get_start();
        $categoryPath = $this->request->get('category_path');
        switch ($this->request->get('sort')) {
            case 'hot':
                $sort = 'hotness';
                break;
            case 'votes':
                $sort = 'netvotes';
                break;
            case 'answers':
                $sort = 'acount';
                break;
            case 'views':
                $sort = 'views';
                break;
            default:
                $sort = 'created';
        }

        $questions = qa_db_select_with_pending(qa_db_qs_selectspec(
            qa_get_logged_in_userid(),
            $sort,
            $start,
            $categoryPath,
            null,
            false,
            false,
            qa_opt_if_loaded('page_size_qs')
        ));
        $favourites = qa_get_favorite_non_qs_map();

        return new QuestionsListResponse($questions, $favourites);
    }
}
