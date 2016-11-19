<?php

$loader = new \Phalcon\Loader();

/**
 * We're a registering a set of directories taken from the configuration file
 */
$loader->registerDirs([
	$config->application->controllersDir,
	$config->application->modelsDir,
	$config->application->libraryDir,
	$config->application->pluginsDir,
]);
$loader->registerNamespaces([
	'Phalcon' => $config->application->libraryDir,
	'Chan\Models' => $config->application->modelsDir,
	'Chan\Controllers' => $config->application->controllersDir,
	'Chan\Controllers\Api' => $config->application->controllersDir.'api/'
]);

$loader->register();
