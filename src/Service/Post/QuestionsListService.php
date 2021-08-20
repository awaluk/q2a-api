<?php

namespace Q2aApi\Service\Post;

use Q2aApi\Base\Paginator;
use Q2aApi\Helper\CategoryHelper;
use Q2aApi\Http\Request;

class QuestionsListService
{
    public function getList(Request $request): array
    {
        $userId = qa_get_logged_in_userid();
        $page = !empty($request->has('page')) ? (int)$request->get('page') : 1;

        if ($request->has('tag')) {
            $tagData = qa_db_select_with_pending(qa_db_tag_word_selectspec($request->get('tag')));
            $itemsPerPage = qa_opt('page_size_tag_qs');
            $paginator = new Paginator($tagData['tagcount'] ?? 0, $itemsPerPage, $page);

            return [
                qa_db_select_with_pending(qa_db_tag_recent_qs_selectspec(
                    $userId,
                    $request->get('tag'),
                    $paginator->getFirstItem() - 1,
                    false,
                    $itemsPerPage
                )),
                $paginator
            ];
        }

        if (in_array($request->get('unanswered'), [true, 'true'], true)) {
            $itemsPerPage = qa_opt('page_size_una_qs');
            $paginator = new Paginator(qa_opt('cache_unaqcount'), $itemsPerPage, $page);

            return [
                qa_db_select_with_pending(qa_db_unanswered_qs_selectspec(
                    $userId,
                    'acount',
                    $paginator->getFirstItem() - 1,
                    [],
                    false,
                    false,
                    $itemsPerPage
                )),
                $paginator
            ];
        }

        if ($request->has('category')) {
            $categorySlugs = CategoryHelper::pathToSlugs($request->get('category'));
            $categoryData = qa_db_select_with_pending(qa_db_full_category_selectspec($categorySlugs, false));
        }
        $itemsPerPage = qa_opt('page_size_qs');
        $paginator = new Paginator($categoryData['qcount'] ?? qa_opt('cache_qcount'), $itemsPerPage, $page);

        return [
            qa_db_select_with_pending(qa_db_qs_selectspec(
                $userId,
                $this->getSortField($request->get('sort')),
                $paginator->getFirstItem() - 1,
                $categorySlugs ?? [],
                null,
                false,
                false,
                $itemsPerPage
            )),
            $paginator
        ];
    }

    public function getHomeList(): array
    {
        $userId = qa_get_logged_in_userid();
        list($questions1, $questions2) = qa_db_select_with_pending(
            qa_db_qs_selectspec($userId, 'created', 0, [], null, false, false, qa_opt_if_loaded('page_size_activity')),
            qa_db_recent_a_qs_selectspec($userId, 0, [])
        );

        return qa_any_sort_and_dedupe(array_merge($questions1, $questions2));
    }

    public function getActivityList(): array
    {
        $userId = qa_get_logged_in_userid();
        list($questions1, $questions2, $questions3, $questions4) = qa_db_select_with_pending(
            qa_db_qs_selectspec($userId, 'created', 0, [], null, false, false, qa_opt_if_loaded('page_size_activity')),
            qa_db_recent_a_qs_selectspec($userId, 0, []),
            qa_db_recent_c_qs_selectspec($userId, 0, []),
            qa_db_recent_edit_qs_selectspec($userId, 0, [])
        );

        return qa_any_sort_and_dedupe(array_merge($questions1, $questions2, $questions3, $questions4));
    }

    private function getSortField(string $field = null): string
    {
        $mappings = [
            'hot' => 'hotness',
            'votes' => 'netvotes',
            'answers' => 'acount',
            'views' => 'views',
        ];

        return $mappings[$field] ?? 'created';
    }
}
