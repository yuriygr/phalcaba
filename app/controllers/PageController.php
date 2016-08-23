<?php

class PageController extends ControllerBase
{
	public function indexAction()
	{
		$this->view->disableLevel(Phalcon\Mvc\View::LEVEL_LAYOUT);

		$slogan = Phalcon\Text::dynamic("
			{Все для тебя|Твоя анонимность|Твоя рулетка|Твой чятик|Сиди тут|Смейся|Мята|Зачем тебе картинки},
			{Карасик|Голова Отца|>Анон|Собака|Покемон|ЕФГ|Виталик|Зой}
		");
		$this->view->setVars([
			'slogan'  => $slogan
		]);
	}

	public function show404Action()
	{
		$this->tag->prependTitle('Страница не найдена');
		$this->response->setStatusCode(404, "Not Found");
	}

	public function showAction()
	{
		$slug = $this->dispatcher->getParam('slug');

		// Параметры для выборки постов
		$parameter = [ 'slug = :slug:', 'bind' => [ 'slug' => $slug ]];

		// Выбираем данные
		$page = Page::findFirst($parameter);

		// Проверка на наличие траницы
		if (!$page)
			return $this->_returnNotFound();

		// Создаем переменные для шаблона
		$this->view->setVar('page', $page);

		// Меняем заголовок
		$this->tag->prependTitle($page->name);

		// Ну и метатеги
		if ($page->meta_description)
			$this->tag->setDescription($page->meta_description);
		if ($page->meta_keywords)
			$this->tag->setKeywords($page->meta_keywords);
	}
}