<?php

/*
    Plugin Name: Q2A API
    Plugin URI: https://github.com/awaluk/q2a-api
    Plugin Description: Creates simple API and serve some data as JSON
    Plugin Version: 0.1.0
    Plugin Date: 2020-08-29
    Plugin Author: Arkadiusz Waluk
    Plugin Author URI: https://waluk.pl
    Plugin License: MIT
    Plugin Update Check URI: https://raw.githubusercontent.com/awaluk/q2a-api/master/metadata.json
    Plugin Minimum Question2Answer Version: 1.7
    Plugin Minimum PHP Version: 7.0
*/

if (!defined('QA_VERSION')) {
    header('Location: ../../');
    exit();
}

define('API_URL', 'api/');
qa_register_plugin_module('page', 'src/api-favorites.php', 'api_favorites', 'API favorites');
qa_register_plugin_phrases('lang/*.php', 'q2a_api');

require_once 'src/api-functions.php';
