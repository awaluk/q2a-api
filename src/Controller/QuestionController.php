<?php

namespace Q2aApi\Controller;

use Q2aApi\Base\AbstractController;
use Q2aApi\Dto\QuestionDto;
use Q2aApi\Exception\ForbiddenHttpException;
use Q2aApi\Exception\NotFoundHttpException;
use Q2aApi\Exception\BadRequestHttpException;
use Q2aApi\Http\Response;
use Q2aApi\Response\QuestionResponse;
use Q2aApi\Response\VoteResponse;

class QuestionController extends AbstractController
{
    public function show(int $questionId): Response
    {
        $userId = qa_get_logged_in_userid();
        $questionData = qa_db_select_with_pending(qa_db_full_post_selectspec($userId, $questionId));
        if ($questionData === null || $questionData['basetype'] !== QuestionDto::TYPE_QUESTION) {
            throw new NotFoundHttpException();
        }
        $question = new QuestionDto($questionData);

        $createdByUser = qa_post_is_by_user($questionData, $userId, qa_cookie_get());
        $canHideShow = qa_user_permit_error($createdByUser ? null : 'permit_hide_show');
        $canModerate = qa_user_permit_error('permit_moderate');
        $canView = $question->isHidden() ? !$canHideShow : (!$question->isQueued() || $createdByUser || !$canModerate);
        if ($canView === false) {
            throw new ForbiddenHttpException();
        }

        return new QuestionResponse($question, qa_get_favorite_non_qs_map(), true);
    }
    
    public function vote(int $questionId): Response
    {
        $vote = $this->request->get('vote');
        if (!in_array($vote, [-1, 0, 1])) {
            throw new BadRequestHttpException();
        }

        $userId = qa_get_logged_in_userid();
        $cookieId = qa_cookie_get();
        $post = qa_db_select_with_pending(qa_db_full_post_selectspec($userId, $questionId));
        if ($post === null || $post['basetype'] !== QuestionDto::TYPE_QUESTION) {
            throw new NotFoundHttpException();
        }

        require_once QA_INCLUDE_DIR.'app/votes.php';
        $voteError = qa_vote_error_html($post, $vote, $userId, qa_request());
        if (!empty($voteError)) {
            throw new ForbiddenHttpException();
        }

        qa_vote_set($post, $userId, qa_get_logged_in_handle(), $cookieId, $vote);
        return new VoteResponse($vote);
    }
}
