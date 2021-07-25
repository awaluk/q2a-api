<?php

use Q2aApi\Base\Router;
use Q2aApi\Exception\HttpException;
use Q2aApi\Http\JsonResponse;
use Q2aApi\Http\Request;

class api_page
{
    public function match_request($request)
    {
        $address = explode('/', $request);

        return isset($address[0]) && $address[0] === API_URL;
    }

    public function process_request($requestUrl)
    {
        try {
            $request = new Request($requestUrl);
            list($class, $method, $parameters) = (new Router())->match($request);
            $controller = new $class($request);
            $response = $controller->{$method}(...$parameters);
        } catch (HttpException $exception) {
            $response = $exception->getJsonResponse();
        } catch (Error $e) {
            $response = new JsonResponse(
                ['message' => qa_lang('q2a_api/response_internal_server_error')],
                JsonResponse::STATUS_INTERNAL_SERVER_ERROR
            );
        }

        http_response_code($response->getStatus());
        foreach ($response->getHeaders() as $header) {
            header($header);
        }
        if (!empty(qa_opt('api_cors_origin'))) {
            header('Access-Control-Allow-Origin: ' . qa_opt('api_cors_origin'));
            header('Access-Control-Allow-Credentials: true');
        }
        echo $response->getBody();
    }
}
