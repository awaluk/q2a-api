<?php

namespace Q2aApi\Response;

use Q2aApi\Dto\QuestionDto;
use Q2aApi\Dto\AnswerDto;
use Q2aApi\Dto\CommentDto;
use Q2aApi\Dto\PostDto;
use Q2aApi\Http\JsonResponse;
use Q2aApi\Http\ResponseBodyFunctionInterface;

class QuestionResponse extends JsonResponse implements ResponseBodyFunctionInterface
{
    private $question;
    private $answers;
    private $comments;
    private $favourites;
    private $full;

     /**
     * @param AnswerDto[] $answers
     * @param CommentDto[] $comments
     */
    public function __construct(
        QuestionDto $question,
        array $answers,
        array $comments,
        array $favourites = [],
        bool $full = false
    )
    {
        $this->question = $question;
        $this->answers = $answers;
        $this->comments = $comments;
        $this->favourites = $favourites;
        $this->full = $full;

        parent::__construct();
    }

    public function data(): array
    {
        if ($this->full === false) {
            return $this->getPostKeys($this->question);
        }
    
        return array_merge(
            $this->getPostKeys($this->question),
            $this->getQuestionKeys($this->question)
        );
    }

    private function getPostKeys(PostDto $post): array
    {
        return [
            'author' => [
                'id' => $post->getAuthor()->getId(),
                'name' => $post->getAuthor()->getName(),
                'title' => $post->getAuthor()->getPointsTitle(),
                'points' => $post->getAuthor()->getPoints(),
                'level' => $post->getAuthor()->getLevel(),
                'favourite' => isset($this->favourites['user'][$post->getAuthor()->getId()])
            ],
            'change' => $this->getChange($post),
            'content' => $post->getContent(),
            'contentType' => $post->hasHtmlContent() ? 'html' : 'text',
            'createDate' => $post->getCreatedDate(),
            'id' => $post->getId(),
            'isHidden' => $post->isHidden(),
            'userVote' => $post->getUserVote(),
            'votesCount' => $post->getVotesSum()
        ];
    }

    private function getQuestionKeys(QuestionDto $question): array
    {
         return [
            'answers' => $this->getAnswers(),
            'answersCount' => $question->getAnswersCount(),
            'category' => [
                'id' => $question->getCategory()->getId(),
                'title' => $question->getCategory()->getName(),
                'path' => $question->getCategory()->getPath(),
                'favourite' => isset($this->favourites['category'][$question->getOriginal('categorybackpath')])
            ],
            'closed' => $question->isClosed(),
            'favourite' => $question->isFavouriteForLoggedUser(),
            'hasBestAnswer' => $question->hasBestAnswer(),
            'slug' => $question->getSlug(),
            'tags' => $this->getTags($this->question),
            'title' => $question->getTitle(),
            'viewsCount' => $question->getViewsNumber()
        ];
    }

    private function getAnswerKeys(AnswerDto $answer): array
    {
        return [
            'comments' => $this->getComments($answer),
            'isBestAnswer' => $answer->getId() === $this->question->getBestAnswerId()
        ];
    }

    private function getAnswers(): array
    {
        $answers = array_map(function ($answer) {
            return array_merge(
                $this->getPostKeys($answer),
                $this->getAnswerKeys($answer)
            );
        }, $this->answers);

        return array_values($answers);
    }

    private function getComments(AnswerDto $answer): array
    {
        $answerComments = array_filter($this->comments, function ($comment) use ($answer) {
            return $comment->getParentId() === $answer->getId();
        });

        $comments = array_map(function ($comment) {
            return $this->getPostKeys($comment);
        }, $answerComments);

        return array_values($comments);
    }

    private function getTags(QuestionDto $question): array
    {
        return array_map(function ($tag) {
            return [
                'name' => $tag,
                'favourite' => isset($this->favourites['tag'][$tag])
            ];
        }, $question->getTags());
    }

    private function getUser(PostDto $post, $prefix = ''): array
    {
        $options = qa_post_html_options($post->getOriginals());

        return [
            'id' => (int)$post->getOriginal($prefix . 'userid'),
            'name' => $post->getOriginal($prefix . 'handle'),
            'title' => qa_get_points_title_html($post->getOriginal($prefix . 'points'), $options['pointstitle']),
            'points' => (int)$post->getOriginal($prefix . 'points'),
            'level' => (int)$post->getOriginal($prefix . 'level'),
            'favourite' => isset($this->favourites['user'][$post->getOriginal($prefix . 'userid')])
        ];
    }

    private function getChange(PostDto $post): array {
        return [
            'type' => $this->getLatestChangeType($post),
            'user' => $post->hasOriginal('ouserid') && $post->getOriginal('oupdatetype') !== null
                ? ($post->getOriginal('ouserid') !== null ? $this->getUser($post, 'o') : null)
                : ($post->getOriginal('userid') !== null ? $this->getUser($post) : null),
            'date' => date('c', $post->getOriginal('otime') ?? $post->getOriginal('created')),
            'showItemId' => $post->hasOriginal('opostid') && $post->getOriginal('obasetype') !== 'Q'
                ? (int)$post->getOriginal('opostid')
                : null
        ];
    }

    private function getLatestChangeType(PostDto $post): string
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

        $type = $post->getOriginal('obasetype') ?? $post->getType();

        if (!empty($post->getOriginal('oupdatetype'))) {
            $type .= '_' . $post->getOriginal('oupdatetype');
        }

        if ($type === 'Q_C' && $post instanceof QuestionDto) {
            return $post->isClosed() ? 'question_closed' : 'question_reopened';
        }

        if (in_array($type, ['Q_H', 'A_H', 'C_H'])) {
            $suffix = $post->isHidden() ? '_hidden' : '_restored';
            return ($type === 'Q_H' ? 'question' : ($type === 'A_H' ? 'answer' : 'comment')) . $suffix;
        }

        return $actions[$type];
    }
}
