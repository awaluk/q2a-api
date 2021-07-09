<?php

class api_page
{
    public function match_request($request)
    {
        $address = explode('/', $request);

        return isset($address[0]) && $address[0] === API_URL;
    }

    public function process_request($requestUrl)
    {
        require_once 'src/AbstractController.php';
        require_once 'src/Request.php';
        require_once 'src/Response.php';
        require_once 'src/JsonResponse.php';
        require_once 'src/Router.php';
        require_once 'src/Exceptions/HttpException.php';
        require_once 'src/Exceptions/NotFoundHttpException.php';

        try {
            $request = new \Q2aApi\Request($requestUrl);
            list($file, $class, $method) = (new \Q2aApi\Router())->match($request);
            require_once 'src/Controllers/' . $file;
            $controller = new $class($request);
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
            header('Access-Control-Allow-Credentials: true');
        }
        echo $response->getBody();
    }
}
