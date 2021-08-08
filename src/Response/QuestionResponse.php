<?php

namespace Q2aApi\Response;

use Q2aApi\Dto\QuestionDto;
use Q2aApi\Http\JsonResponse;
use Q2aApi\Http\ResponseBodyFunctionInterface;

class QuestionResponse extends JsonResponse implements ResponseBodyFunctionInterface
{
    private $question;
    private $favourites;
    private $full;

    public function __construct(QuestionDto $question, array $favourites = [], bool $full = false)
    {
        $this->question = $question;
        $this->favourites = $favourites;
        $this->full = $full;

        parent::__construct();
    }

    public function data(): array
    {
        $data = [
            'id' => $this->question->getId(),
            'title' => $this->question->getTitle(),
            'slug' => $this->question->getSlug(),
            'answers' => $this->question->getAnswersCount(),
            'votes' => $this->question->getVotesSum(),
            'views' => $this->question->getViewsNumber(),
            'favourite' => $this->question->isFavouriteForLoggedUser(),
            'userVote' => $this->question->getUserVote(),
            'closed' => $this->question->isClosed(),
            'hasBestAnswer' => $this->question->hasBestAnswer(),
            'createDate' => $this->question->getCreatedDate(),
            'tags' => array_map(function ($tag) {
                return [
                    'name' => $tag,
                    'favourite' => isset($this->favourites['tag'][$tag])
                ];
            }, $this->question->getTags()),
            'category' => [
                'id' => $this->question->getCategory()->getId(),
                'title' => $this->question->getCategory()->getName(),
                'path' => $this->question->getCategory()->getPath(),
                'favourite' => isset($this->favourites['category'][$this->question->getOriginal('categorybackpath')])
            ],
            'change' => [
                'type' => $this->getLatestChangeType(),
                'user' => $this->question->hasOriginal('ouserid') && $this->question->getOriginal('oupdatetype') !== null
                    ? ($this->question->getOriginal('ouserid') !== null ? $this->getUser('o') : null)
                    : ($this->question->getOriginal('userid') !== null ? $this->getUser() : null),
                'date' => date('c', $this->question->getOriginal('otime') ?? $this->question->getOriginal('created')),
                'showItemId' => $this->question->hasOriginal('opostid') && $this->question->getOriginal('obasetype') !== 'Q'
                    ? (int)$this->question->getOriginal('opostid')
                    : null
            ],
        ];
        if ($this->full === false) {
            return $data;
        }

        return array_merge($data, [
            'author' => [
                'id' => $this->question->getAuthor()->getId(),
                'name' => $this->question->getAuthor()->getName(),
                'title' => $this->question->getAuthor()->getPointsTitle(),
                'points' => $this->question->getAuthor()->getPoints(),
                'level' => $this->question->getAuthor()->getLevel(),
                'favourite' => isset($this->favourites['user'][$this->question->getAuthor()->getId()])
            ],
            'isHidden' => $this->question->isHidden(),
            'contentType' => $this->question->hasHtmlContent() ? 'html' : 'text',
            'content' => $this->question->getContent(),
        ]);
    }

    private function getUser(string $prefix = ''): array
    {
        $options = qa_post_html_options($this->question->getOriginals());

        return [
            'id' => (int)$this->question->getOriginal($prefix . 'userid'),
            'name' => $this->question->getOriginal($prefix . 'handle'),
            'title' => qa_get_points_title_html($this->question->getOriginal($prefix . 'points'), $options['pointstitle']),
            'points' => (int)$this->question->getOriginal($prefix . 'points'),
            'level' => (int)$this->question->getOriginal($prefix . 'level'),
            'favourite' => isset($this->favourites['user'][$this->question->getOriginal($prefix . 'userid')])
        ];
    }

    private function getLatestChangeType(): string
    {
        $actions = [
            'C_Y' => 'answer_changed_to_comment',
            'C_M' => 'comment_moved',
            'A_S' => 'answer_selected',
            'Q_A' => 'question_category_updated',
            'Q_T' => 'question_tags_updated',
            'C_E' => 'comment_updated',
            'A_E' => 'answer_updated',
            'Q_E' => 'question_updated',
            'C' => 'comment_created',
            'A' => 'answer_created',
            'Q' => 'question_created',
        ];

        $type = $this->question->getOriginal('obasetype') ?? $this->question->getType();
        if (!empty($this->question->getOriginal('oupdatetype'))) {
            $type .= '_' . $this->question->getOriginal('oupdatetype');
        }

        if ($type === 'Q_C') {
            return $this->question->isClosed() ? 'question_closed' : 'question_reopened';
        }
        if (in_array($type, ['Q_H', 'A_H', 'C_H'])) {
            $suffix = $this->question->isHidden() ? '_hidden' : '_restored';
            return ($type === 'Q_H' ? 'question' : ($type === 'A_H' ? 'answer' : 'comment')) . $suffix;
        }

        return $actions[$type];
    }
}
