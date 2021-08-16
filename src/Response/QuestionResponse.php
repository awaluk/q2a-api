<?php

namespace Q2aApi\Response;

use Q2aApi\Dto\QuestionDto;
use Q2aApi\Dto\AnswerDto;
use Q2aApi\Dto\CommentDto;
use Q2aApi\Dto\PostDto;
use Q2aApi\Dto\UserDto;
use Q2aApi\Http\JsonResponse;
use Q2aApi\Http\ResponseBodyFunctionInterface;
use Q2aApi\Service\PostService;
use Q2aApi\Service\UserService;

class QuestionResponse extends JsonResponse implements ResponseBodyFunctionInterface
{
    private $question;
    private $answers;
    private $comments;
    private $favourites;
    private $userService;
    private $postService;

    /**
     * @param QuestionDto $question
     * @param AnswerDto[] $answers
     * @param CommentDto[] $comments
     * @param array $favourites
     */
    public function __construct(
        QuestionDto $question,
        array $answers,
        array $comments,
        array $favourites = []
    ) {
        $this->question = $question;
        $this->answers = $answers;
        $this->comments = $comments;
        $this->favourites = $favourites;
        $this->userService = new UserService();
        $this->postService = new PostService();

        parent::__construct();
    }

    public function data(): array
    {
        return array_merge(
            $this->getPostKeys($this->question),
            $this->getQuestionKeys($this->question)
        );
    }

    private function getPostKeys(PostDto $post): array
    {
        return [
            'id' => $post->getId(),
            'content' => $post->getContent(),
            'contentType' => $post->hasHtmlContent() ? 'html' : 'text',
            'author' => $this->getUserKeys($post->getAuthor()),
            'change' => $this->getChange($post),
            'createDate' => $post->getCreatedDate(),
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

    private function getUserKeys(?UserDto $user): ?array
    {
        if ($user === null) {
            return null;
        }

        return [
            'id' => $user->getId(),
            'name' => $user->getName(),
            'title' => $user->getPointsTitle(),
            'points' => $user->getPoints(),
            'level' => $user->getLevel(),
            'favourite' => isset($this->favourites['user'][$user->getId()])
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

    private function getChange(PostDto $post): ?array
    {
        if (in_array($this->postService->getLatestActionType($post), [
            'question_created',
            'answer_created',
            'comment_created'
        ])) {
            return null;
        }
        $updateType = $post->getOriginal('oupdatetype') ?? $post->getOriginal('updatetype');
        if ($updateType === 'E' && ($post->getOriginal('updated') - $post->getOriginal('created') < 300)) {
            return null;
        }

        $changeUserHandle = $post->hasOriginal('ohandle')
            ? $post->getOriginal('ohandle')
            : $post->getOriginal('lasthandle');

        $changeUser = $this->userService->getByHandle($changeUserHandle);

        return [
            'type' => $this->postService->getLatestActionType($post),
            'user' => $this->getUserKeys($changeUser),
            'date' => date('c', $post->getOriginal('updated')),
            'showItemId' => null
        ];
    }
}
