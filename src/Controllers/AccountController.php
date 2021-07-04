<?php

namespace Q2aApi;

class AccountController extends AbstractController
{
    public function account(): Response
    {
        return $this->json([
            'id' => (int)qa_get_logged_in_userid(),
            'handle' => qa_get_logged_in_handle(),
            'email' => qa_get_logged_in_email(),
            'level' => (int)qa_get_logged_in_level(),
            'points' => (int)qa_get_logged_in_points(),
        ]);
    }

    public function favourites(): Response
    {
        $userId = qa_get_logged_in_userid();

        $dbQuestions = qa_db_select_with_pending(qa_db_user_favorite_qs_selectspec($userId));
        $questions = array_column($dbQuestions, 'postid');
        $questions = array_map('intval', $questions);

        $dbUsers = qa_db_select_with_pending(qa_db_user_favorite_users_selectspec($userId));
        $users = array_column($dbUsers, 'handle');

        $dbTags = qa_db_select_with_pending(qa_db_user_favorite_tags_selectspec($userId));
        $tags = array_column($dbTags, 'word');

        $dbCategories = qa_db_select_with_pending(qa_db_user_favorite_categories_selectspec($userId));
        $categories = array_column($dbCategories, 'title');

        return $this->json([
            'questions' => $questions,
            'users' => $users,
            'tags' => $tags,
            'categories' => $categories,
        ]);
    }
}
