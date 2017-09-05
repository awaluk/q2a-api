<?php
/**
 * Q2A API - plugin to Question2Answer
 * @author Arkadiusz Waluk <arkadiusz@waluk.pl>
 */

class api_favorites
{
    public function match_request($request)
    {
        return $request === API_URL.'favorites';
    }

    public function process_request()
    {
        $user_id = qa_get_logged_in_userid();
        if (empty($user_id)) {
            return_json_response(['error' => 'User not logged']);
        }

        $db_questions = qa_db_select_with_pending(qa_db_user_favorite_qs_selectspec($user_id));
        $questions = array_column($db_questions, 'postid');

        $db_users = qa_db_select_with_pending(qa_db_user_favorite_users_selectspec($user_id));
        $users = array_column($db_users, 'handle');

        $db_tags = qa_db_select_with_pending(qa_db_user_favorite_tags_selectspec($user_id));
        $tags = array_column($db_tags, 'word');

        $db_categories = qa_db_select_with_pending(qa_db_user_favorite_categories_selectspec($user_id));
        $categories = array_column($db_categories, 'title');

        return_json_response([
            'questions' => $questions,
            'users' => $users,
            'tags' => $tags,
            'categories' => $categories,
        ]);
    }
}
