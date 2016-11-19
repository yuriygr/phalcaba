<?php

namespace Chan\Controllers;

use \Chan\Models\News;

class NewsController extends ControllerBase
{
	public function listAction()
	{
		$currentPage = $this->request->getQuery('page', 'int');
		if ($currentPage <= 0) $currentPage = 1;

		// Выбираем данные
		$news = News::find([ 'order' => 'created_at DESC' ]);

		// Проверка на наличие поста
		if (!$news)
			return $this->_returnNotFound();

		// Разделяем на страницы
		$paginator = new \Phalcon\Paginator\Adapter\Model([
			'data' 	=> $news,
			'limit'	=> $this->config->site->newsLimit,
			'page' 	=> $currentPage
		]);
		$news = $paginator->getPaginate();

		// Создаем переменные для шаблона
		$this->view->setVar('news', $news);

		// Устанавливаем заголовок
		$this->tag->prependTitle('News');
	}

	public function showAction()
	{
		$slug = $this->dispatcher->getParam('slug');

		// Выбираем данные
		$news = News::findFirstBySlug($slug);

		// Проверка на наличие новости
		if (!$news)
			return $this->_returnNotFound();

		// Создаем переменные для шаблона
		$this->view->setVar('news', $news);

		// Устанавливаем заголовок
		$this->tag->prependTitle($news->title);
		// Устанавливаем описание
		$this->tag->setDescription($news->description);
	}
}