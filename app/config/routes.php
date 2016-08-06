<?php
// Home
$router->add( '/', 'Page::index' )
	   ->setName('home-link');
	   
// Page
$router->add( '/page/{slug}', 'Page::show' )
	   ->setName('page-link');
$router->add( '/page/404', 'Page::show404' )
	   ->setName('page-404');

// Chan
$router->add( '/{board:[a-z]+}/', 'Chan::board' )
	   ->setName('board-link');

$router->add( '/{board:[a-z]+}/add', 'Chan::add' )
	   ->setName('add-link');

$router->add( '/{board:[a-z]+}/page/{page:[0-9]+}', 'Chan::board' )
	   ->setName('chan-page-link');

$router->add( '/{board:[a-z]+}/thread/{id:[0-9]+}', 'Chan::thread' )
	   ->setName('thread-link');

$router->add( '/{board:[a-z]+}/catalog', 'Chan::catalog' )
	   ->setName('chan-catalog-link');

// News
$router->add( '/news/', 'News::list' )
	   ->setName('news-list');

$router->add( '/news/{slug}', 'News::show' )
	   ->setName('news-link');

// 404 page
$router->notFound( 'Page::show404' );