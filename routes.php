<?php

return [
    ['path' => 'login', 'method' => 'post', 'action' => 'AuthController::login'],

    ['path' => 'categories', 'method' => 'get', 'action' => 'CategoriesController::list'],
    ['path' => 'statistics', 'method' => 'get', 'action' => 'StatisticsController::get'],

    ['path' => 'account', 'method' => 'get', 'action' => 'AccountController::account', 'auth' => true],
    ['path' => 'favourites', 'method' => 'get', 'action' => 'AccountController::favourites', 'auth' => true],
];
