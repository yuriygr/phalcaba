<?php

namespace Chan\Controllers;

use \Phalcon\Tag;
use \Phalcon\Mvc\View;

use \Chan\Models\Chan;
use \Chan\Models\Page;

class ControllerBase extends \Phalcon\Mvc\Controller
{
	public function initialize()
	{
		$this->tag->setAutoescape(false);
		$this->tag->setDocType(Tag::HTML5);
		$this->tag->setFavicon($this->config->site->favicon);
		$this->tag->setTitleSeparator(' - ');
		$this->tag->setGenerator('Phalcaba ' . $this->config->application->version);

		$this->tag->setTitle($this->config->site->title);
		$this->tag->setDescription($this->config->site->description);
		$this->tag->setKeywords($this->config->site->keywords);

		// Список всех досок, страниц и название чана
		$boardsList 	= Chan::find('isHide != 1');
		$pagesList  	= Page::find('isHide != 1');

		$this->view->setVars([
			'chanName'		=> $this->config->site->title,
			'boardsList'  	=> $boardsList ?? [],
			'pagesList'   	=> $pagesList ?? [],
			'maxFiles'  	=> $this->config->site->maxFiles,
			'allowedFiles'  => $this->config->site->allowedFiles->toArray()
		]);

			 
	}
	/*
	 * Ахтунг! Возвращает json контент
	 */
	public function _returnJson($array)
	{
		$this->view->disable();
		$this->response->setContentType('application/json', 'UTF-8');
		$this->response->setJsonContent($array);
		return false;
	}
	
	public function _returnNotFound()
	{
		$this->tag->prependTitle('Страница не найдена');
		$this->response->setStatusCode(404, "Not Found");
		//	
		$this->dispatcher->setControllerName('page');
		$this->dispatcher->setActionName('show404');
	}

	public function _redirectHome()
	{
		return $this->response->redirect($this->url->get([ 'for' => 'chan.home' ]));
	}
}