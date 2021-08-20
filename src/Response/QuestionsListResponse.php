<?php

namespace Q2aApi\Response;

use Q2aApi\Base\Paginator;
use Q2aApi\Model\Post\Question;
use Q2aApi\Http\JsonResponse;
use Q2aApi\Http\ResponseBodyFunctionInterface;
use Q2aApi\Service\PostService;

class QuestionsListResponse extends JsonResponse implements ResponseBodyFunctionInterface
{
    private $paginator;
    private $questions;
    private $favourites;
    private $postService;

    public function __construct(array $questions, array $favourites = [], Paginator $paginator = null)
    {
        $this->questions = $questions;
        $this->favourites = $favourites;
        $this->paginator = $paginator;
        $this->postService = new PostService();

        parent::__construct();
    }

    public function data(): array
    {
        $data = [];
        foreach ($this->questions as $question) {
            $data[] = $this->getQuestionData(new Question($question));
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

    private function getQuestionData(Question $question): array
    {
        return [
            'id' => $question->getId(),
            'title' => $question->getTitle(),
            'slug' => $question->getSlug(),
            'answersCount' => $question->getAnswersCount(),
            'votesCount' => $question->getVotesSum(),
            'viewsCount' => $question->getViewsNumber(),
            'favourite' => $question->isFavouriteForLoggedUser(),
            'closed' => $question->isClosed(),
            'hasBestAnswer' => $question->hasBestAnswer(),
            'createDate' => $question->getCreatedDate(),
            'tags' => array_map(function ($tag) {
                return [
                    'name' => $tag,
                    'favourite' => isset($this->favourites['tag'][$tag])
                ];
            }, $question->getTags()),
            'category' => [
                'id' => $question->getCategory()->getId(),
                'title' => $question->getCategory()->getName(),
                'path' => $question->getCategory()->getPath(),
                'favourite' => isset($this->favourites['category'][$question->getOriginal('categorybackpath')])
            ],
            'change' => [
                'type' => $this->postService->getLatestActionType($question),
                'user' => $question->hasOriginal('ouserid') && $question->getOriginal('otime') !== null
                    ? ($question->getOriginal('ouserid') !== null ? $this->getUser($question, 'o') : null)
                    : ($question->getOriginal('userid') !== null ? $this->getUser($question) : null),
                'date' => date('c', $question->getOriginal('otime') ?? $question->getOriginal('created')),
                'showItemId' => $question->hasOriginal('opostid') && $question->getOriginal('obasetype') !== 'Q'
                    ? (int)$question->getOriginal('opostid')
                    : null
            ],
        ];
    }

    private function getUser(Question $question, string $prefix = ''): array
    {
        $options = qa_post_html_options($question->getOriginals());

        return [
            'id' => (int)$question->getOriginal($prefix . 'userid'),
            'name' => $question->getOriginal($prefix . 'handle'),
            'title' => qa_get_points_title_html($question->getOriginal($prefix . 'points'), $options['pointstitle']),
            'points' => (int)$question->getOriginal($prefix . 'points'),
            'level' => (int)$question->getOriginal($prefix . 'level'),
            'favourite' => isset($this->favourites['user'][$question->getOriginal($prefix . 'userid')])
        ];
    }
}
