<?php

/*
    Plugin Name: Q2A API
    Plugin URI: https://github.com/awaluk/q2a-api
    Plugin Description: Adds simple API to serve some data as JSON
    Plugin Version: 0.2.0
    Plugin Date: 2020-08-29
    Plugin Author: Arkadiusz Waluk
    Plugin Author URI: https://waluk.pl
    Plugin License: MIT
    Plugin Update Check URI: https://raw.githubusercontent.com/awaluk/q2a-api/master/metadata.json
    Plugin Minimum Question2Answer Version: 1.7
    Plugin Minimum PHP Version: 7.1
*/

if (!defined('QA_VERSION')) {
    header('Location: ../../');
    exit();
}

require_once __DIR__ . '/vendor/autoload.php';

define('API_URL', 'api');
qa_register_plugin_module('page', 'modules/page.php', 'api_page', 'API page');
qa_register_plugin_module('page', 'modules/admin.php', 'api_admin', 'API admin');
qa_register_plugin_phrases('lang/*.php', 'q2a_api');
