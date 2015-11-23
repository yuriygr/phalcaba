<?php

return new \Phalcon\Config(array(
	'site' => array(
		'title'				=> 'Phalcaba',
		'description'		=> 'Open engine anonymous image board',
		'keywords'			=> 'Движок, чан, изображения, анонимность, АИБ, форум',

		// Кол-во ответов к треду на главной странице
		'replyLimit'		=> '4',
		// Кол-во тредов на странице
		'threadLimit'		=> '15',
		// Кол-во постов в треде
		'postLimit'			=> '500',
		// Кол-во символов в заголовке
		'subjectLimit'		=> '50',
		// Имя по умолчанию
		'defalutName'		=> 'Аноним',
		// Разрешённые к загрузке файлы
		'allowedFormats'	=> 'jpg, jpeg, png, gif'
	),
	'database' => array(
		'adapter'			=> 'Mysql',
		'host'				=> 'localhost',
		'username'			=> 'root',
		'password'			=> 'password',
		'name'				=> 'base',
	),
	'application' => array(
		'controllersDir'	=> __DIR__ . '/../../app/controllers/',
		'modelsDir'			=> __DIR__ . '/../../app/models/',
		'viewsDir'			=> __DIR__ . '/../../app/views/',
		'libraryDir'		=> __DIR__ . '/../../app/library/',
		'baseUri'			=> '/',
		'cryptSalt'			=> 'eE_&,+v]:-3d-*A&Sy|:+.u>/6m,$D',
		'version'			=> '0.3',
	),
));
