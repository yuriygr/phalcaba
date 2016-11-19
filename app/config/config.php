<?php

$config =  new \Phalcon\Config([
	'site' => [
		'favicon'			=> '/static/img/favicon.ico',
		'title'				=> 'Phalcaba',
		'description'		=> 'Open. Free. Simple.',
		'keywords'			=> 'imageboard, chan, board, 4chan, 2channel, 2ch, 0chan, борды, хуёрды',

		// Кол-во ответов к треду на главной странице
		'replyLimit'		=> '4',
		// Кол-во тредов на странице
		'threadLimit'		=> '15',
		// Кол-во страниц в чане
		'pageLimit'			=> '8',
		// Кол-во постов в треде
		'postLimit'			=> '501',
		// Кол-во новостей на страницу
		'newsLimit'			=> '10',
		// Кол-во символов в заголовке
		'subjectLimit'		=> '65',
		// Имя по умолчанию
		'defalutName'		=> 'Anonymous',
		// Разрешённые к загрузке файлы
		'allowedFiles' 		=> ['image/jpg', 'image/jpeg', 'image/png', 'image/gif'],
		// Сколько файлов можно аттачить к посту
		'maxFiles' 			=> '1',
	],
	'database' => [
		'adapter'			=> 'Mysql',
		'host'				=> '127.0.0.1',
		'username'			=> '',
		'password'			=> '',
		'name'				=> '',
		'charset'			=> 'utf8'
	],
	'redis' => [
		'host'				=> '127.0.0.1',
		'port'				=> 6379,
		'lifetime'			=> 129600
	],
	'application' => [
		'controllersDir'	=> APP_DIR  . '/controllers/',
		'modelsDir'			=> APP_DIR  . '/models/',
		'viewsDir'			=> APP_DIR  . '/views/',
		'libraryDir'		=> APP_DIR  . '/library/',
		'pluginsDir'		=> APP_DIR  . '/plugins/',
		'filesDir'			=> BASE_DIR . '/public/file/',
		'cacheDir'			=> BASE_DIR . '/cache/',
		'baseUri'			=> '/',
		'cryptSalt'			=> 'SALT',
		'version'			=> '2.1.0'
	],
]);
