<?php

namespace Q2aApi\Response;

use Q2aApi\Base\Paginator;
use Q2aApi\Helper\QuestionHelper;
use Q2aApi\Http\JsonResponse;
use Q2aApi\Http\ResponseBodyFunctionInterface;

class QuestionsListResponse extends JsonResponse implements ResponseBodyFunctionInterface
{
    private $paginator;
    private $questions;
    private $favourites;

    public function __construct(Paginator $paginator, array $questions, array $favourites = [])
    {
        $this->paginator = $paginator;
        $this->questions = $questions;
        $this->favourites = $favourites;

        parent::__construct();
    }

    public function data(): array
    {
        $data = [];
        foreach ($this->questions as $question) {
            $data[] = $this->getQuestionItem($question);
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

    private function getQuestionItem(array $question)
    {
        $options = qa_post_html_options($question);

        return [
            'id' => (int)$question['postid'],
            'title' => $question['title'],
            'slug' => QuestionHelper::titleToSlug($question['title']),
            'answers' => (int)$question['acount'],
            'votes' => (int)$question['netvotes'],
            'views' => (int)$question['views'],
            'favourite' => isset($question['userfavoriteq']) && $question['userfavoriteq'] === '1',
            'closed' => $question['closedbyid'] !== null,
            'hasBestAnswer' => $question['selchildid'] !== null,
            'tags' => array_map(function ($tag) {
                return [
                    'name' => $tag,
                    'favourite' => isset($this->favourites['tag'][$tag])
                ];
            }, QuestionHelper::tagsStringToArray($question['tags'])),
            'category' => [
                'id' => $question['categoryid'],
                'title' => $question['categoryname'],
                'path' => $question['categorybackpath'],
                'favourite' => isset($this->favourites['category'][$question['categorybackpath']])
            ],
            'change' => [
                'type' => 'question_created',
                'user' => [
                    'id' => (int)$question['userid'],
                    'name' => $question['handle'],
                    'title' => qa_get_points_title_html($question['points'], $options['pointstitle']),
                    'points' => $question['points'],
                    'level' => $question['level'],
                    'favourite' => isset($this->favourites['user'][$question['userid']])
                ],
                'date' => date('c', $question['created']),
                'showItemId' => null
            ]
        ];
    }
}
