<?php

$config =  new \Phalcon\Config([
	'site' => [
		'favicon'			=> '/assert/favicon.ico',
		'title'				=> 'Phalcaba',
		'description'		=> 'Open. Free. Clear.',
		'keywords'			=> 'Движок, чан, изображения, анонимность, АИБ, форум, открытый',

		// Кол-во ответов к треду на главной странице
		'replyLimit'		=> '4',
		// Кол-во тредов на странице
		'threadLimit'		=> '10',
		// Кол-во постов в треде
		'postLimit'			=> '500',
		// Кол-во символов в заголовке
		'subjectLimit'		=> '60',
		// Имя по умолчанию
		'defalutName'		=> 'Аноним',
		// Разрешённые к загрузке файлы
		'allowedFormats'	=> 'jpg, jpeg, png, gif'
	],
	'database' => [
		'adapter'			=> 'Mysql',
		'host'				=> 'localhost',
		'username'			=> '',
		'password'			=> '',
		'name'				=> 'chan',
		'charset'			=> 'utf8',
	],
	'redis' => [
		'host'				=> '127.0.0.1',
		'port'				=> 6379,
		'lifetime'			=> 129600,
	],
	'application' => [
		'controllersDir'	=> APP_DIR . '/controllers/',
		'modelsDir'			=> APP_DIR . '/models/',
		'viewsDir'			=> APP_DIR . '/views/',
		'libraryDir'		=> APP_DIR . '/library/',
		'pluginsDir'		=> APP_DIR . '/plugins/',
		'cacheDir'			=> APP_DIR . '/../cache/',
		'baseUri'			=> '/',
		'cryptSalt'			=> 'e*A&SSd*d8s78d($D',
		'version'			=> '1.0',
	],
]);
