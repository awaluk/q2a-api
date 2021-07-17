<?php

namespace Q2aApi\Controller;

use Q2aApi\Base\AbstractController;
use Q2aApi\Base\Paginator;
use Q2aApi\Helper\CategoryHelper;
use Q2aApi\Http\Response;
use Q2aApi\Response\QuestionsListResponse;

class QuestionsController extends AbstractController
{
    public function list(): Response
    {
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

        $categoryPath = $this->request->get('category');
        if ($categoryPath !== null) {
            $categorySlugs = CategoryHelper::pathToSlugs($categoryPath);
            $categoryData = qa_db_select_with_pending(qa_db_full_category_selectspec($categorySlugs, false));
        }

        $paginator = new Paginator(
            $categoryData['qcount'] ?? qa_opt('cache_qcount'),
            qa_opt('page_size_qs'),
            !empty($this->request->get('page')) ? (int)$this->request->get('page') : 1
        );
        $questions = qa_db_select_with_pending(qa_db_qs_selectspec(
            qa_get_logged_in_userid(),
            $sort,
            $paginator->getFirstItem() - 1,
            $categorySlugs ?? [],
            null,
            false,
            false,
            qa_opt('page_size_qs')
        ));
        $favourites = qa_get_favorite_non_qs_map();

        return new QuestionsListResponse($paginator, $questions, $favourites);
    }
}
