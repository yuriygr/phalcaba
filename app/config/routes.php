<?php

/*
	Page
	==================================================================================
*/
$router->add( '/', 'Page::index' )
	   ->setName('chan.home');
$router->add( '/page/{slug}', 'Page::show' )
	   ->setName('chan.page');
$router->add( '/page/404', 'Page::show404' )
	   ->setName('chan.page.404');

/*
	News
	==================================================================================
*/
$router->add( '/news', 'News::list' )
	   ->setName('chan.news');

$router->add( '/news/{slug}', 'News::show' )
	   ->setName('chan.news.link');

/*
	Chan
	==================================================================================
*/
$router->add( '/{board:[a-z]+}/', 'Chan::board' )
	   ->setName('chan.board');

$router->add( '/{board:[a-z]+}/add', 'Chan::add' )
	   ->setName('chan.thread.add');

$router->add( '/{board:[a-z]+}/page/{page:[0-9]+}', 'Chan::board' )
	   ->setName('chan.board.page');

$router->add( '/{board:[a-z]+}/thread/{id:[0-9]+}', 'Chan::thread' )
	   ->setName('chan.thread.link');

$router->add( '/{board:[a-z]+}/catalog', 'Chan::catalog' )
	   ->setName('chan.board.catalog');

$router->add( '/{board:[a-z]+}/search', 'Chan::search' )
	   ->setName('chan.search');
$router->add( '/{board:[a-z]+}/search?hashtag={hashtag}', 'Chan::search' )
	   ->setName('chan.search.hashtag');

/*
	Panel
	==================================================================================
*/
$router->add( '/panel', [
	'namespace'  => 'Chan\Controllers\Panel',
	'controller' => 'Page',
	'action'     => 'index',
]);
$router->add( '/panel/reports', [
	'namespace'  => 'Chan\Controllers\Panel',
	'controller' => 'Reports',
	'action'     => 'index',
]);

/*
	API
	==================================================================================
*/
// Post
$router->add( '/api/getPost', [
	'namespace'  => 'Chan\Controllers\Api',
	'controller' => 'Post',
	'action'     => 'get',
]);
// Thread
$router->add( '/api/expandThread', [
	'namespace'  => 'Chan\Controllers\Api',
	'controller' => 'Thread',
	'action'     => 'expand',
]);
$router->add( '/api/refreshThread', [
	'namespace'  => 'Chan\Controllers\Api',
	'controller' => 'Thread',
	'action'     => 'refresh',
]);
$router->add( '/api/followThread', [
	'namespace'  => 'Chan\Controllers\Api',
	'controller' => 'Thread',
	'action'     => 'follow',
]);
$router->add( '/api/hideThread', [
	'namespace'  => 'Chan\Controllers\Api',
	'controller' => 'Thread',
	'action'     => 'hide',
]);


// 404 page
$router->notFound( 'Page::show404' );