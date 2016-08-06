<?php

class NewsController extends ControllerBase
{
	public function initialize()
    {
    	parent::initialize();
    }
	public function listAction()
	{
		$currentPage = $this->request->getQuery('page', 'int', 1);

		// Параметры для выборки постов
		$parameter = [ 'order' => 'created_at DESC' ];

		// Выбираем данные
		$news = News::find($parameter);

		// Проверка на наличие поста
		if (!$news)
			return $this->_returnNotFound();

		// Разделяем на страницы
		$paginator = new \Phalcon\Paginator\Adapter\Model([
			'data' 	=> $news,
			'limit'	=> $this->config->site->postLimit,
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