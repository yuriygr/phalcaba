<?php


ini_set('display_errors',1);
error_reporting(E_ALL);
date_default_timezone_set('Europe/Moscow');

try {

	/**
	 * Read the configuration
	 */
	$config = include(__DIR__.'/../app/config/config.php');


	/**
	 * We're a registering a set of directories taken from the configuration file. And namespaces too
	 */
	$loader = new \Phalcon\Loader();
	$loader->registerDirs(
		array(
			$config->application->controllersDir,
			$config->application->modelsDir,
			$config->application->libraryDir,
		)
	);
	$loader->registerNamespaces(
		array(
			'Phalcon' => $config->application->libraryDir
		)
	);
	$loader->register();


	/**
	 * The FactoryDefault Dependency Injector automatically register the right services providing a full stack framework
	 */
	$di = new \Phalcon\DI\FactoryDefault();

	/**
	 * For use in controllers
	 */	
	$di->setShared('config', $config);
	
	/**
	 * Include the application routes
	 */
	$di->set('router', function() {
		$router = new \Phalcon\Mvc\Router(false);
		include(__DIR__.'/../app/config/routes.php');
		return $router;
	});

	/**
	 * The URL component is used to generate all kind of urls in the application
	 */
	$di->set('url', function() use ($config) {
		$url = new \Phalcon\Mvc\Url();
		// Устанавливаем базовый путь
		$url->setBaseUri($config->application->baseUri);
		return $url;
	});

	/**
	 * Setting up the view component
	 */
	$di->set('view', function() use ($config) {
		$view = new \Phalcon\Mvc\View();
		// Устанавливаем директорию с шаблонами по умочанию
		$view->setViewsDir($config->application->viewsDir);
		return $view;
	});

	/**
	 * Database connection is created based in the parameters defined in the configuration file
	 */
	$di->set('db', function() use ($config) {
		return new \Phalcon\Db\Adapter\Pdo\Mysql(array(
			'host'		=> $config->database->host,
			'username'	=> $config->database->username,
			'password'	=> $config->database->password,
			'dbname' 	=> $config->database->name,
			'charset'   => 'utf8'
		));
	});
	
	/*
	 * Poeben' for normal catching 404
	 */
	$di->set('dispatcher', function() use ($di) {
			$evManager = $di->getShared('eventsManager');
			$evManager->attach(
				"dispatch:beforeException",
				function($event, $dispatcher, $exception)
				{
					switch ($exception->getCode()) {
						case \Phalcon\Mvc\Dispatcher::EXCEPTION_HANDLER_NOT_FOUND:
						case \Phalcon\Mvc\Dispatcher::EXCEPTION_ACTION_NOT_FOUND:
							$dispatcher->forward(
								array(
									'controller' => 'pages',
									'action'     => 'show404',
								)
							);
							return false;
					}
				}
			);
			$dispatcher = new \Phalcon\Mvc\Dispatcher();
			$dispatcher->setEventsManager($evManager);
			return $dispatcher;
		},
		true
	);


	/**
	 * Request
	 */
	$di->set('request', function() {
		return new \Phalcon\Http\Request;
	});

	/**
	 * Start the session the first time some component request the session service
	 */
	$di->set('session', function() {
		$session = new \Phalcon\Session\Adapter\Files();
		$session->start();
		return $session;
	});
	
	/**
	 * Печенье
	 */	
	$di->set('cookies', function () {
		$cookies = new Phalcon\Http\Response\Cookies();
		$cookies->useEncryption(true);
		return $cookies;
	});

	/**
	 * Create a filter
	 */
	$di->set('filter', function() {
		return new \Phalcon\Filter();
	});
	
	/**
	 * New Tag
	 */	
	$di->set('tag', function() {
		return new \Phalcon\NTag();
	});

	/**
	 * О да!
	 */	
	$di->set('crypt', function () use ($config) {
		$crypt = new \Phalcon\Crypt();
		$crypt->setKey($config->application->cryptSalt);
		return $crypt;
	});

	Phalcon\Mvc\Model::setup(array(
		'notNullValidations' => false
	));


	/**
	 * Handle the request
	 */
	$application = new \Phalcon\Mvc\Application();
	$application->setDI($di);
	echo $application->handle()->getContent();

} catch (Phalcon\Exception $e) {
	echo $e->getMessage();
}