<?php

declare(strict_types=1);

namespace Q2aApi\Service\Post;

use Q2aApi\Model\Post\Answer;
use Q2aApi\Model\Post\Comment;
use Q2aApi\Model\Post\Question;

class QuestionViewService
{
    private $userId;

    public function __construct()
    {
        $this->userId = qa_get_logged_in_userid();
    }

    public function findQuestion(int $id): ?Question
    {
        $questionData = qa_db_select_with_pending(qa_db_full_post_selectspec($this->userId, $id));

        if ($questionData === null || $questionData['basetype'] !== Question::TYPE_QUESTION) {
            return null;
        }

        return new Question($questionData);
    }

    public function hasPermissionToShow(Question $question)
    {
        $createdByUser = qa_post_is_by_user($question->getOriginals(), $this->userId, qa_cookie_get());
        $canHideShow = qa_user_permit_error($createdByUser ? null : 'permit_hide_show');
        $canModerate = qa_user_permit_error('permit_moderate');

        return $question->isHidden() ? !$canHideShow : (!$question->isQueued() || $createdByUser || !$canModerate);
    }

    public function getAnswers(Question $question): array
    {
        $answers = qa_db_select_with_pending(qa_db_full_child_posts_selectspec($this->userId, $question->getId()));

        if (qa_opt('sort_answers_by') === 'votes') {
            foreach ($answers as $answerId => $answer) {
                $answers[$answerId]['sortvotes'] = $answer['downvotes'] - $answer['upvotes'];
            }
            qa_sort_by($answers, 'sortvotes', 'created');
        } else {
            qa_sort_by($answers, 'created');
        }

        return array_map(function ($answer) {
            return new Answer($answer);
        }, $answers);
    }

    public function getComments(Question $question): array
    {
        $comments = qa_db_select_with_pending(qa_db_full_a_child_posts_selectspec($this->userId, $question->getId()));

        return array_map(function ($comment) {
            return new Comment($comment);
        }, $comments);
    }
}
