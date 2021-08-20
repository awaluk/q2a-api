<?php

namespace Q2aApi\Service;

use Q2aApi\Model\User;

class UserService
{
    public function __construct()
    {
        require_once QA_INCLUDE_DIR . 'db/users.php';
    }

    public function getById(?string $userId): ?User
    {
        if (empty($userId)) {
            return null;
        }

        $userData = qa_db_select_with_pending(qa_db_user_account_selectspec($userId, true));

        if (empty($userData)) {
            return null;
        }

        return new User($userData);
    }

    public function getByHandle(?string $handle): ?User
    {
        if (empty($handle)) {
            return null;
        }

        $userId = qa_db_user_find_by_handle($handle)[0];

        return $this->getById($userId);
    }
}
