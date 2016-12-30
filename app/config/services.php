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
$di->set('router', function() use ($config) {
	$router = new \Phalcon\Mvc\Router(false);
	$router->removeExtraSlashes(false);
	include($config->application->configDir . '/routes.php');
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

	$view->registerEngines([
		'.volt' => function($view, $di) use ($config) {
			$volt = new \Phalcon\Mvc\View\Engine\Volt($view, $di);
			$volt->setOptions([
				'compiledPath' => $config->application->cacheDir . 'volt/',
				'compiledSeparator' => '_',
			]);
			return $volt;
		},
		'.phtml' => 'Phalcon\Mvc\View\Engine\Php' // Generate Template files uses PHP itself as the template engine
	]);
	return $view;
});

/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$di->setShared('db', function() use ($config) {
	return new \Phalcon\Db\Adapter\Pdo\Mysql([
		'host'		=> $config->database->host,
		'username'	=> $config->database->username,
		'password'	=> $config->database->password,
		'dbname' 	=> $config->database->name,
		'charset'	=> $config->database->charset,
		'options'	=> [
			 \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
			 \PDO::ATTR_PERSISTENT => true,
			 \PDO::ATTR_AUTOCOMMIT => false
		]
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
		'host' 			=> $config->redis->host,
		'port' 			=> $config->redis->port,
		'persistent' 	=> 0,
		'statsKey' 		=> '_PHCM_MM',
		'lifetime' 		=> $config->redis->lifetime
	]);
});*/

/*
 * Диспатчер
 */
$di->set('dispatcher', function() use ($di) {
	$eventsManager = new \Phalcon\Events\Manager;

	// Catch not found error
	$eventsManager->attach('dispatch:beforeException', new NotFoundPlugin);

	$dispatcher = new \Phalcon\Mvc\Dispatcher();
	$dispatcher->setEventsManager($eventsManager);
	$dispatcher->setDefaultNamespace('Chan\Controllers');
	return $dispatcher;
});

/**
 * Request
 */
$di->set('request', function() {
	return new \Phalcon\Http\Request;
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
 * WakabaMark parser
 */
$di->set('parse', function() {
	$parse = new \Phalcon\Utils\Parse();
	return $parse;
});

/**
 * File uploader and thumb creater
 */
$di->set('uploader', function() {
	return new \Phalcon\Utils\Uploader\Uploader();
});

/**
 * Assets manager and default settings
 */
$di->set('assets', function() {
	$assets = new \Phalcon\NAssets();
	
	// JS
	$assets
		 ->collection('app-js')
		 ->addJs('static/js/jquery-3.1.1.min.js')
		 ->addJs('static/js/jquery.core.js')
		 ->addJs('static/js/jquery.store.js')
		 ->addJs('static/js/jquery.attachFile.js')
		 ->addJs('static/js/jquery.magnific-popup.min.js')
		 ->addJs('static/js/jquery.ambiance.js')
		 ->addJs('static/js/main.js')
		/*
		 ->setTargetPath('static/app.js')
		 ->setTargetUri('static/app.js')
		 ->join(true)
		 ->addFilter(new \Phalcon\Assets\Filters\Jsmin())*/;

	// Fonts
	$assets
		 ->collection('app-fonts')
		 ->addCss('//fonts.googleapis.com/css?family=Open+Sans:300,400,600&amp;subset=latin,cyrillic-ext', false, false);

	// CSS
	$assets
		 ->collection('app-css')
		 ->addCss('static/css/reset.css')
		 ->addCss('static/css/style.css')
		 ->addCss('static/css/magnific-popup.css')
		/*
		 ->setTargetPath('static/app.css')
		 ->setTargetUri('static/app.css')
		 ->join(true)
		 ->addFilter(new \Phalcon\Assets\Filters\Cssmin())*/;

	return $assets;
});

/**
 * Security
 */
$di->set('security', function () {
	$security = new \Phalcon\Security();
	$security->setWorkFactor(12);
	return $security;
});

/**
 * Crypt
 */
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

/**
 * Create Authorization
 */
$di->set('auth', function() {
	return new \Phalcon\Authorization();
});


\Phalcon\Mvc\Model::setup([
	'notNullValidations' => true
]);