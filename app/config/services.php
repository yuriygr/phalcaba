<?php

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
	$router->removeExtraSlashes(false);
	include(APP_DIR . '/config/routes.php');
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

	$view->registerEngines(array(
		'.volt' => function($view, $di) use ($config) {
			$volt = new \Phalcon\Mvc\View\Engine\Volt($view, $di);
			$volt->setOptions(array(
				'compiledPath' => $config->application->cacheDir . 'volt/',
				'compiledSeparator' => '_',
			));
			return $volt;
		},
		'.phtml' => 'Phalcon\Mvc\View\Engine\Php' // Generate Template files uses PHP itself as the template engine
	));
	return $view;
});

/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$di->set('db', function() use ($config) {
	return new \Phalcon\Db\Adapter\Pdo\Mysql([
		'host'		=> $config->database->host,
		'username'	=> $config->database->username,
		'password'	=> $config->database->password,
		'dbname' 	=> $config->database->name,
		'charset'	=> $config->database->charset
	]);
});

/**
 * If the configuration specify the use of metadata adapter use it or use memory otherwise
 */
/*$di->set('modelsMetadata', function () use ($config) {
	return new \Phalcon\Mvc\Model\Metadata\Files(array(
		'metaDataDir' => $config->application->cacheDir . 'metaData/'
	));
	return new \Phalcon\Mvc\Model\Metadata\Redis([
		'host' 			=> '127.0.0.1',
		'port' 			=> 6379,
		'persistent' 	=> 0,
		'statsKey' 		=> '_PHCM_MM',
		'lifetime' 		=> 172800
	]);
});*/

/*
 * Диспатчер
 */
$di->set('dispatcher', function() use ($di) {
		$evManager = $this->getShared('eventsManager');
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
);/*
$di->set('dispatcher', function() use ($di) {
	$eventsManager = new \Phalcon\Events\Manager;

	// Отлавливаем ошибку 404
	$eventsManager->attach('dispatch:beforeException', new NotFoundPlugin);

	$dispatcher = new \Phalcon\Mvc\Dispatcher();
	$dispatcher->setEventsManager($eventsManager);
	return $dispatcher;
});*/

/**
 * Request
 */
$di->set('request', function() {
	return new \Phalcon\Http\Request;
});

/**
 * Register the flash service with custom CSS classes
 */
$di->set('flash', function() {
	return new \Phalcon\Flash\Direct([
		'error' => 'alert error',
		'success' => 'alert success',
		'notice' => 'alert info',
	]);
});
/* And Session Flas */
$di->set('flashSession', function() {
	return new \Phalcon\Flash\Session([
		'error' => 'alert error',
		'success' => 'alert success',
		'notice' => 'alert info',
	]);
});

/**
 * Start the session the first time some component request the session service
 */
$di->set('session', function() use ($config) {
	/*$session = new \Phalcon\Session\Adapter\Files();*/
	/*$session = new \Phalcon\Session\Adapter\Redis([
        'path'		=> 'tcp://127.0.0.1:6379?weight=1',
		'lifetime'  => 7200
    ]);*/
	$session = new \Phalcon\Session\Adapter\Redis([
		'host'       => $config->redis->host,
		'port'       => $config->redis->port,
		'lifetime'   => $config->redis->lifetime,
	]);
	$session->start();
	return $session;
});

/**
 * Парсер
 */
$di->set('parse', function() {
	$parse = new \Phalcon\Utils\Parse;
	return $parse;
});

$di->set('crypt', function () use ($config) {
	$crypt = new \Phalcon\Crypt();
	$crypt->setKey($config->application->cryptSalt);
	return $crypt;
});


/**
 * New Tag
 */	
$di->set('tag', function() {
	return new \Phalcon\NTag();
});


Phalcon\Mvc\Model::setup([ 'notNullValidations' => true ]);


?>