<?php

class ControllerBase extends \Phalcon\Mvc\Controller
{
	public function initialize()
	{
		$this->tag->setAutoescape(false);
		$this->tag->setDocType(Phalcon\Tag::HTML5);
		$this->tag->setFavicon($this->config->site->favicon);
		$this->tag->setTitleSeparator(' - ');
		$this->tag->setGenerator('Phalcaba ' . $this->config->application->version);

		// Записываем метатеги
		$this->tag->setTitle($this->config->site->title);
		$this->tag->setDescription($this->config->site->description);
		$this->tag->setKeywords($this->config->site->keywords);

		$this->assets
			 ->collection('app-js')

			 ->addJs('https://code.jquery.com/jquery-3.0.0.min.js', false, false)
			 ->addJs('https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/jquery.magnific-popup.min.js', false, false)
			 ->addJs('assets/js/jquery.ambiance.js')
			 ->addJs('assets/js/jquery.core.js')
			 ->addJs('assets/js/main.js')/*

			 ->setTargetPath('assets/app.js')
			 ->setTargetUri('assets/app.js')
			 
			 ->join(true)
			 ->addFilter(new Phalcon\Assets\Filters\Jsmin())*/;

		$this->assets
			 ->collection('app-css')

			 ->addCss('https://fonts.googleapis.com/css?family=Open+Sans:300,400,600&amp;subset=latin,cyrillic-ext', false, false)
			 ->addCss('https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/magnific-popup.min.css', false, false)
			 ->addCss('assets/css/reset.css')
			 ->addCss('assets/css/style.css')/*

			 ->setTargetPath('assets/app.css')
			 ->setTargetUri('assets/app.css')

			 ->join(true)
			 ->addFilter(new Phalcon\Assets\Filters\Cssmin())*/;

		// Список всех досок
		$this->boards = Chan::find('isHide != 1');
		$this->pages  = Page::find();
		$this->view->setVars([
			'boards'  => $this->boards,
			'pages'   => $this->pages
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
		$this->tag->prependTitle('Страница не найдена - бейс');
		$this->response->setStatusCode(404, "Not Found");
		//	
		$this->dispatcher->setControllerName('page');
		$this->dispatcher->setActionName('show404');
	}
}