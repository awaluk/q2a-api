<?php

namespace Q2aApi\Response;

use Q2aApi\Helper\QuestionHelper;
use Q2aApi\Http\JsonResponse;
use Q2aApi\Http\ResponseBodyFunctionInterface;

class QuestionsListResponse extends JsonResponse implements ResponseBodyFunctionInterface
{
    private $items;
    private $favourites;

    public function __construct(array $items, array $favourites = [])
    {
        $this->items = $items;
        $this->favourites = $favourites;

        parent::__construct();
    }

    public function data(): array
    {
        $questions = [];
        foreach ($this->items as $question) {
            $options = qa_post_html_options($question);
            $questions[] = [
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

        return $questions;
    }
}
