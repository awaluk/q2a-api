<?php

namespace Q2aApi\Controller;

use Q2aApi\Base\AbstractController;
use Q2aApi\Exception\BadRequestHttpException;
use Q2aApi\Exception\ForbiddenHttpException;
use Q2aApi\Exception\NotFoundHttpException;
use Q2aApi\Http\Request;
use Q2aApi\Http\Response;
use Q2aApi\Model\Post\Question;
use Q2aApi\Response\QuestionResponse;
use Q2aApi\Response\VoteResponse;
use Q2aApi\Service\Post\QuestionViewService;

class QuestionController extends AbstractController
{
    private $service;

    public function __construct(Request $request)
    {
        $this->service = new QuestionViewService();
        parent::__construct($request);
    }

    public function show(int $questionId): Response
    {
        $question = $this->service->findQuestion($questionId);
        if ($question === null) {
            throw new NotFoundHttpException();
        }
        if ($this->service->hasPermissionToShow($question) === false) {
            throw new ForbiddenHttpException();
        }

        $answers = $this->service->getAnswers($question);
        $comments = $this->service->getComments($question);

        return new QuestionResponse($question, $answers, $comments, qa_get_favorite_non_qs_map());
    }

    public function vote(int $questionId): Response
    {
        $userVote = $this->request->get('vote');
        if (!in_array($userVote, [-1, 0, 1])) {
            throw new BadRequestHttpException();
        }

        $userId = qa_get_logged_in_userid();
        $cookieId = qa_cookie_get();
        $post = qa_db_select_with_pending(qa_db_full_post_selectspec($userId, $questionId));
        if ($post === null || $post['basetype'] !== Question::TYPE_QUESTION) {
            throw new NotFoundHttpException();
        }

        require_once QA_INCLUDE_DIR . 'app/votes.php';
        $voteError = qa_vote_error_html($post, $userVote, $userId, qa_request());
        if (!empty($voteError)) {
            throw new ForbiddenHttpException();
        }

        qa_vote_set($post, $userId, qa_get_logged_in_handle(), $cookieId, $userVote);

        $updatedPost = qa_db_select_with_pending(qa_db_full_post_selectspec($userId, $questionId));
        $question = new Question($updatedPost);
        $votes = $question->getVotesSum();

        return new VoteResponse($userVote, $votes);
    }
}
