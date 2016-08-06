<?php

ini_set('display_errors',1);
error_reporting(E_ALL);
date_default_timezone_set('Europe/Moscow');

try {
    /**
     * Define App directory
     */
	define('APP_DIR', realpath('../app'));

	/**
	 * Read the configuration
	 */
	include(APP_DIR . '/config/config.php');

	/**
	 * Read auto-loader
	 */
	include(APP_DIR . '/config/loader.php');

	/**
	 * Read services
	 */
	include(APP_DIR . '/config/services.php');

	/**
	 * Handle the request
	 */
	$application = new \Phalcon\Mvc\Application($di);
	echo $application->handle()->getContent();

} catch (Phalcon\Exception $e) {
	echo $e->getMessage();
}