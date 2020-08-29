<?php

function return_json_response($data, $code = 200)
{
    header('Content-Type: application/json');
    http_response_code($code);

    echo json_encode($data);
    exit();
}
