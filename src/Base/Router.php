<?php

namespace Q2aApi\Base;

use Q2aApi\Exception\HttpException;
use Q2aApi\Exception\NotFoundHttpException;
use Q2aApi\Http\Request;
use Q2aApi\Http\Response;

class Router
{
    private $routes;

    public function __construct()
    {
        $this->routes = require_once __DIR__ . '/../../routes/routes.php';
    }

    public function match(Request $request): array
    {
        foreach ($this->routes as $route) {
            if ($route['path'] === $request->getPath() && strtolower($route['method']) === $request->getMethod()) {
                if (isset($route['auth']) && $route['auth'] === true && qa_is_logged_in() === false) {
                    throw new HttpException(qa_lang('q2a_api/response_unauthorized'), Response::STATUS_UNAUTHORIZED);
                }

                return $this->getParams($route['action']);
            }
        }

        throw new NotFoundHttpException();
    }

    private function getParams(string $action): array
    {
        list($controller, $method) = explode('::', $action);

        return [
            "Q2aApi\\Controller\\{$controller}", // class
            $method // method
        ];
    }
}
