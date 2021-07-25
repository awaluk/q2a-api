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
            $pathCheck = $this->checkPath($route, $request->getPath());
            if ($pathCheck !== false && strtolower($route['method']) === $request->getMethod()) {
                if (isset($route['auth']) && $route['auth'] === true && qa_is_logged_in() === false) {
                    throw new HttpException(qa_lang('q2a_api/response_unauthorized'), Response::STATUS_UNAUTHORIZED);
                }

                return $this->getParams($route['action'], is_array($pathCheck) ? $pathCheck : []);
            }
        }

        throw new NotFoundHttpException();
    }

    private function checkPath(array $route, string $path)
    {
        if (!empty($route['parameters'])) {
            $regex = $route['path'];
            foreach ($route['parameters'] as $parameterName => $parameterRegex) {
                $regex = str_replace('{' . $parameterName . '}', '(' . $parameterRegex . ')', $regex);
            }
            $result = preg_match("@^{$regex}$@", $path, $parameterValues);
            unset($parameterValues[0]);

            return $result === 1 ? $parameterValues : false;
        }

        return $route['path'] === $path;
    }

    private function getParams(string $action, array $parameters = []): array
    {
        list($controller, $method) = explode('::', $action);

        return [
            "Q2aApi\\Controller\\{$controller}", // class
            $method, // method
            $parameters
        ];
    }
}
