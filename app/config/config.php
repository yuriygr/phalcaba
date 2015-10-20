<?php

return new \Phalcon\Config(array(
	'site' => array(
		'title'				=> 'Phalcaba',
		'description'		=> 'Phalcaba',
		'keywords'			=> 'Phalcaba',

		'replyLimit'		=> '4',
		'threadLimit'		=> '10',
		'postLimit'			=> '500',
		'defalutName'		=> 'Аноним',
		'allowedFormats'	=> 'jpg, jpeg, png, gif'
	),
	'database' => array(
		'adapter'			=> 'Mysql',
		'host'				=> '',
		'username'			=> '',
		'password'			=> '',
		'name'				=> '',
	),
	'application' => array(
		'controllersDir'	=> __DIR__ . '/../../app/controllers/',
		'modelsDir'			=> __DIR__ . '/../../app/models/',
		'viewsDir'			=> __DIR__ . '/../../app/views/',
		'libraryDir'		=> __DIR__ . '/../../app/library/',
		'baseUri'			=> '/',
		'cryptSalt'			=> 'eE_&,+v]:-3d-*A&Sy|:+.u>/6m,$D',
		'version'			=> '0.2b',
	),
));
