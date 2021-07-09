<?php

namespace Q2aApi;

class Router
{
    public function match(string $url): array
    {
        if ($url === 'categories') {
            return $this->getParams('CategoriesController::list');
        }
        if ($url === 'statistics') {
            return $this->getParams('StatisticsController::get');
        }
        if ($url === 'login') {
            return $this->getParams('AuthController::login');
        }

        if (!qa_is_logged_in()) {
            throw new HttpException(qa_lang('q2a_api/response_unauthorized'), Response::STATUS_UNAUTHORIZED);
        }

        if ($url === 'account') {
            return $this->getParams('AccountController::account');
        }
        if ($url === 'favourites') {
            return $this->getParams('AccountController::favourites');
        }

        throw new NotFoundHttpException();
    }

    private function getParams(string $action): array
    {
        list($controller, $method) = explode('::', $action);

        return [
            "{$controller}.php", // file
            "Q2aApi\\{$controller}", // class
            $method // method
        ];
    }
}
