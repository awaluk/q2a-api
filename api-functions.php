<?php
/**
* Q2A API - plugin to Question2Answer
* @author Arkadiusz Waluk <arkadiusz@waluk.pl>
*/

define('API_URL', 'api/');

function return_json_response($data, $code = 200)
{
    header('Content-Type: application/json');
    http_response_code($code);

    echo json_encode($data);
    exit();
}