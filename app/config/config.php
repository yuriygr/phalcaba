<?php

$config =  new \Phalcon\Config([
	'site' => [
		'favicon'			=> '/assets/favicon.ico',
		'title'				=> 'phalcaba',
		'description'		=> 'Open. Free. Clear.',
		'keywords'			=> 'Движок, чан, изображения, анонимность, АИБ, форум, открытый',

		// Кол-во ответов к треду на главной странице
		'replyLimit'		=> '4',
		// Кол-во тредов на странице
		'threadLimit'		=> '15',
		// Кол-во постов в треде
		'postLimit'			=> '501',
		// Кол-во новостей на страницу
		'newsLimit'			=> '10',
		// Кол-во символов в заголовке
		'subjectLimit'		=> '65',
		// Имя по умолчанию
		'defalutName'		=> 'Аноним',
		// Разрешённые к загрузке файлы
		'allowedFiles' 		=> 'jpg, jpeg, png, gif',
		// Сколько файлов можно аттачить к посту
		'countFiles' 		=> '2'
	],
	'database' => [
		'adapter'			=> 'Mysql',
		'host'				=> 'localhost',
		'username'			=> '',
		'password'			=> '',
		'name'				=> '',
		'charset'			=> 'utf8',
	],
	'redis' => [
		'host'				=> '127.0.0.1',
		'port'				=> 6379,
		'lifetime'			=> 129600,
	],
	'application' => [
		'controllersDir'	=> APP_DIR  . '/controllers/',
		'modelsDir'			=> APP_DIR  . '/models/',
		'viewsDir'			=> APP_DIR  . '/views/',
		'libraryDir'		=> APP_DIR  . '/library/',
		'pluginsDir'		=> APP_DIR  . '/plugins/',
		'cacheDir'			=> BASE_DIR . '/cache/',
		'baseUri'			=> '/',
		'cryptSalt'			=> 'SALT',
		'version'			=> '1.0.3',
	],
]);
