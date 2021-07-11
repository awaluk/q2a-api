<?php

namespace Q2aApi\Controller;

use Q2aApi\Base\AbstractController;
use Q2aApi\Http\Response;
use Q2aApi\Response\AccountResponse;

class AuthController extends AbstractController
{
    public function login(): Response
    {
        if (qa_is_logged_in()) {
            return $this->errors([], qa_lang('q2a_api/already_logged_in'), Response::STATUS_FORBIDDEN);
        }

        $login = $this->request->get('login');
        $password = $this->request->get('password');
        $remember = $this->request->get('remember');

        if (strlen($login) === 0 || strlen($password) === 0) {
            return $this->errors(['login' => qa_lang('users/user_not_found')]);
        }

        require_once QA_INCLUDE_DIR . 'app/limits.php';
        if (!qa_user_limits_remaining(QA_LIMIT_LOGINS)) {
            return $this->errors([], qa_lang('users/login_limit'), Response::STATUS_TOO_MANY_REQUESTS);
        }
        require_once QA_INCLUDE_DIR . 'db/users.php';
        require_once QA_INCLUDE_DIR . 'db/selects.php';

        qa_limits_increment(null, QA_LIMIT_LOGINS);

        if (qa_opt('allow_login_email_only') || (strpos($login, '@') !== false)) { // handles can't contain @ symbols
            $found = qa_db_user_find_by_email($login);
        } else {
            $found = qa_db_user_find_by_handle($login);
        }

        if (count($found) !== 1) { // if matches more than one (should be impossible), don't log in
            return $this->errors(['login' => qa_lang('users/user_not_found')]);
        }
        $userid = $found[0];
        $userinfo = qa_db_select_with_pending(qa_db_user_account_selectspec($userid, true));
        if (strtolower(qa_db_calc_passcheck($password, $userinfo['passsalt'])) != strtolower($userinfo['passcheck'])) {
            return $this->errors(['password' => qa_lang('users/password_wrong')]);
        }

        require_once QA_INCLUDE_DIR . 'app/users.php';
        qa_set_logged_in_user($userid, $userinfo['handle'], !empty($remember));

        return new AccountResponse();
    }
}
