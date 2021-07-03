<?php

class api_page
{
    const URL_PREFIX = 'api';

    public function match_request($request)
    {
        $address = explode('/', $request);

        return isset($address[0]) && $address[0] === self::URL_PREFIX;
    }

    public function process_request($request)
    {
        require_once 'src/AbstractController.php';
        require_once 'src/Response.php';
        require_once 'src/JsonResponse.php';
        require_once 'src/Router.php';
        require_once 'src/Exceptions/HttpException.php';
        require_once 'src/Exceptions/NotFoundHttpException.php';

        $url = substr($request, strlen(self::URL_PREFIX) + 1);

        try {
            list($file, $class, $method) = (new \Q2aApi\Router())->match($url);
            require_once 'src/Controllers/' . $file;
            $controller = new $class;
            $response = $controller->{$method}();
        } catch (\Q2aApi\HttpException $exception) {
            $response = $exception->getJsonResponse();
        } catch (Error $e) {
            $response = new \Q2aApi\JsonResponse(
                ['message' => qa_lang('q2a_api/response_internal_server_error')],
                \Q2aApi\JsonResponse::STATUS_INTERNAL_SERVER_ERROR
            );
        }

        http_response_code($response->getStatus());
        foreach ($response->getHeaders() as $header) {
            header($header);
        }
        if (!empty(qa_opt('api_cors_origin'))) {
            header('Access-Control-Allow-Origin: ' . qa_opt('api_cors_origin'));
        }
        echo $response->getBody();
    }
}
