<?php
/**
 * Q2A API - plugin to Question2Answer
 * @author Arkadiusz Waluk <arkadiusz@waluk.pl>
 */

/*
	Plugin Name: Q2A API
	Plugin URI: https://github.com/awaluk/q2a-api
	Plugin Description: Simple API serving data from Q2A in JSON
	Plugin Version: 0.1
	Plugin Date: 2017-09-01
	Plugin Author: Arkadiusz Waluk
	Plugin Author URI: https://waluk.pl
	Plugin License: MIT
	Plugin Minimum Question2Answer Version: 1.5
	Plugin Update Check URI: https://raw.githubusercontent.com/awaluk/q2a-api/master/metadata.json
*/

if (!defined('QA_VERSION')) {
	header('Location: ../../');
	exit;
}

qa_register_plugin_module('page', 'api-favorites.php', 'api_favorites', 'API user favorites');

require_once 'api-functions.php';