<?php

namespace Q2aApi\Response;

use Q2aApi\Base\Paginator;
use Q2aApi\Helper\CategoryHelper;
use Q2aApi\Helper\QuestionHelper;
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
            $data[] = $this->getQuestionItem($question);
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

    private function getQuestionItem(array $question)
    {
        $hasAnswerOnList = isset($question['_type']) && $question['_type'] === 'A';

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
                'id' => (int)$question['categoryid'],
                'title' => $question['categoryname'],
                'path' => CategoryHelper::changeBackPathToPath($question['categorybackpath']),
                'favourite' => isset($this->favourites['category'][$question['categorybackpath']])
            ],
            'change' => [
                'type' => $hasAnswerOnList ? 'answer_created' : 'question_created',
                'user' => $hasAnswerOnList
                    ? ($question['ouserid'] !== null ? $this->getUser($question, 'o') : null)
                    : ($question['userid'] !== null ? $this->getUser($question) : null),
                'date' => date('c', $question['otime'] ?? $question['created']),
                'showItemId' => $hasAnswerOnList ? (int)$question['opostid'] : null
            ]
        ];
    }

    private function getUser(array $question, string $prefix = ''): array
    {
        $options = qa_post_html_options($question);

        return [
            'id' => (int)$question[$prefix . 'userid'],
            'name' => $question[$prefix . 'handle'],
            'title' => qa_get_points_title_html($question[$prefix . 'points'], $options['pointstitle']),
            'points' => (int)$question[$prefix . 'points'],
            'level' => (int)$question[$prefix . 'level'],
            'favourite' => isset($this->favourites['user'][$question[$prefix . 'userid']])
        ];
    }
}
