<?php

/**
 * Error catch
 */
ini_set('display_errors',1);
error_reporting(E_ALL);
date_default_timezone_set('Europe/Moscow');

/**
 * Define App directory
 */
define('BASE_DIR', realpath('../'));
define('APP_DIR', realpath('../app'));
define('PUB_DIR', realpath('../public'));

try {

	/**
	 * Read the configuration
	 */
	include(APP_DIR . '/config/config.php');

	/**
	 * Read auto-loader
	 */
	include(APP_DIR . '/config/loader.php');

	/**
	 * Use composer autoloader to load vendor classes
	 */
	include(BASE_DIR . '/vendor/autoload.php');

	/**
	 * Read services
	 */
	include(APP_DIR . '/config/services.php');

	/**
	 * Create application
	 */
	$application = new \Phalcon\Mvc\Application($di);

	/**
	 * Handle the request
	 */
	echo $application->handle()->getContent();

} catch (Phalcon\Exception $e) {
	echo $e->getMessage();
} catch (PDOException $e) {
	echo $e->getMessage();
}