<?php

namespace Q2aApi;

class Router
{
    public function match(string $url): array
    {
        if (!qa_is_logged_in()) {
            throw new HttpException('Unauthorized', Response::STATUS_UNAUTHORIZED);
        }

        if ($url === 'account') {
            return $this->getParams('AccountController::user');
        }
        if ($url === 'favourites') {
            return $this->getParams('AccountController::favourites');
        }

        throw new NotFoundHttpException();
    }

    private function getParams(string $action): array
    {
        [$controller, $method] = explode('::', $action);

        return [
            "{$controller}.php", // file
            "Q2aApi\\{$controller}", // class
            $method // method
        ];
    }
}
