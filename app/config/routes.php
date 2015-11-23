<?php
// Home
$router->add( '/', 'Pages::index' )
	   ->setName('home-link');
// Pages
$router->add( '/faq', 'Pages::faq' )
	   ->setName('faq-link');
$router->add( '/rules', 'Pages::rules' )
	   ->setName('rules-link');

// Chan
$router->add( '/{board:[a-z]+}/', 'Chan::board' )
	   ->setName('board-link');
$router->add( '/{board:[a-z]+}/add', 'Chan::add' )
	   ->setName('add-link');
$router->add( '/{board:[a-z]+}/page/{page:[0-9]+}', 'Chan::board' )
	   ->setName('page-link');
$router->add( '/{board:[a-z]+}/thread/{id:[0-9]+}', 'Chan::thread' )
	   ->setName('thread-link');
$router->add( '/{board:[a-z]+}/catalog', 'Chan::catalog' )
	   ->setName('catalog-link');
$router->add( '/{board:[a-z]+}/search', 'Chan::search' )
	   ->setName('search-link');

// 404 page
$router->notFound( 'Pages::show404' );