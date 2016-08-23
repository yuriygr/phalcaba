<?php

use \Phalcon\Utils\Slug as Slug;
use \Phalcon\Mvc\View as View;

class ChanController extends ControllerBase
{

	public function initialize()
	{
		parent::initialize();

		$this->board_param 	= $this->dispatcher->getParam('board', 'string');
		$this->thread_param	= $this->dispatcher->getParam('id', 'int', '0');
		
		$this->board = Chan::findFirst(
			[ 'slug = :slug:', 'bind' => [
				'slug' => $this->board_param
			]]
		);
		
		// Если нет такого раздела - разворачиваемся и уходим
		if (!$this->board)
			return $this->_returnNotFound();
			
		$this->tag->setTitle($this->board->name);
	}
	
	public function addAction()
	{
		$this->view->disable();
		
		if ( $this->request->isPost() && $this->request->isAjax() ) {
			$e = new Phalcon\Escaper();
			
			$board_slug = $this->board->slug;
			
			$yarn 		= $this->request->getPost('yarn', 'int');
			
			$kasumi 	= $this->request->getPost('kasumi');
			$kasumi 	= $this->filter->sanitize($kasumi, 'striptags');
			$kasumi 	= $e->escapeHtml($kasumi);
			
			$name 		= null;

			$shampoo 	= $this->request->getPost('shampoo');
			$shampoo	= $this->filter->sanitize($shampoo, 'striptags');
			$shampoo	= $this->parse->make($shampoo, $board_slug, $yarn);
			
			$sage 		= $this->request->getPost('sage') ? 1 : 0;

			$board = Chan::findFirst(
				[ 'slug = :slug:', 'bind' => [
					'slug' => $board_slug
				]]
			);
			
			// Проверка наличия раздела
			if (!$board)
				return $this->_returnJson([ 'error' => 'Такого раздела не существует' ]);

			// Проверка на закрыт ли он
			if ($board->isLocked)
				return $this->_returnJson([ 'error' => 'Раздел закрыт, в него постить нельзя' ]);		
			
			if ($yarn != 0) {
				$thread = Post::findFirst(
					[ 'id = :id: and type = "thread" and board = :board:', 'bind' => [
						'id' => $yarn,
						'board' => $board_slug
					]]
				);
				// Проверка наличия треда
				if (!$thread)
					return $this->_returnJson([ 'error' => 'Такого треда не существует' ]);
				// Проверка на закрыт ли он
				if ($thread->isLocked)
					return $this->_returnJson([ 'error' => 'Тред закрыт, в него постить нельзя' ]);
			}
			
			// Проверка длины заголовка
			if (iconv_strlen($kasumi) >  $this->config->site->subjectLimit)
				return $this->_returnJson([ 'error' => 'Заголовок слишком длинный' ]);
				
			// Проверка на наличие текста
			if (!$shampoo)
				return $this->_returnJson([ 'error' => 'Введите сообщение' ]);


			$post = new Post();
			$post->subject		=	$kasumi ? $kasumi : null;
			$post->timestamp 	=	time();
			$post->name			=	$name;
			$post->text			=	$shampoo;
			$post->type 		= 	($yarn == 0) ? 'thread' : 'reply';
			$post->parent 		= 	$yarn;
			$post->board 		= 	$board_slug;
			$post->owner 		= 	'0';
			$post->bump 		= 	($yarn == 0) ? time() : 0;
			$post->sage 		= 	($yarn == 0) ? 0 : $sage;

			if ($post->save()) {

				// Добавление бампа
				if ($post->parent != 0) {
					$thread = Post::findFirst(
						[ 'id = :id: and board = :board:', 'bind' => [
							'id' => $post->parent,
							'board' => $post->board
						]]
					);
					if ($thread->countReply() < $this->config->site->postLimit && $post->sage == 0)
						$thread->bump = $post->timestamp;

					if (!$thread->update())
						return $this->_returnJson([ 'error' => 'Тред не бампнут, но пост прошёл' ]);
				}

				// Редиректим куда нибудь после поста
				if ($post->parent != 0) {
					// Если добавляется пост, то редирект на пост / TODO: обновляем тред	
					return $this->_returnJson([
						'success' => 'Пост отправлен',
						'sendPost' => ['threadId' => $post->parent, 'postId' => $post->id ],
						'redirect' => $this->url->get([ 'for' => 'chan-thread-link', 'board' => $post->board, 'id' => $post->parent ])
					]);
				} else {
					// Если создаётся тред, то редирект
					return $this->_returnJson([
						'success' => 'Тред создан, перенаправляю',
						'redirect' => $this->url->get([ 'for' => 'chan-thread-link', 'board' => $post->board, 'id' => $post->id ])
					]);
				}
				
			// Если не добавился пост
			} else {
				foreach ($post->getMessages() as $message)
					return $this->_returnJson([ 'error' => (string) $message ]);
			}
			
		}
		return $this->response->redirect($this->url->get([ 'for' => 'home-link' ]));
	}
	
	public function boardAction()
	{	
		// Если нет такого раздела - разворачиваемся и уходим
		if (!$this->board)
			return $this->_returnNotFound();
			
		// Поиск тредов
		$currentPage =  $this->dispatcher->getParam('page', 'int');
		if ($currentPage <= 0) $currentPage = 1;
		$threads = Post::find(
			[ 'type = "thread" and board = :board:', 'order' => 'isSticky DESC, bump DESC', 'bind' => [
				'board' => $this->board->slug
			]]
		);
		$paginator = new \Phalcon\Paginator\Adapter\Model([
			'data' => $threads,
			'limit'=> $this->config->site->threadLimit,
			'page' => $currentPage
		]);
		$threads = $paginator->getPaginate();

		// Название раздела
		$this->tag->prependTitle('/' . $this->board->slug . '/');
		
		// Описание раздела, если есть
		if ($this->board->description)
			$this->tag->setDescription($this->board->description);
		
		// Передаём переменные борда, номер треда и треды
		$this->view->setVars([
			'board' 	=> $this->board,
			'thread_id' => $this->thread_param,
			'threads' 	=> $threads
		]);
	}
	
	public function threadAction()
	{
		// Если нет такого раздела - разворачиваемся и уходим
		if (!$this->board)
			return $this->_returnNotFound();
		
		// Поиск треда
		$thread = Post::findFirst(
			[ 'id = :id: and type = "thread" and board = :board:', 'bind' => [
				'id' 	=> $this->thread_param,
				'board' => $this->board->slug
			]]
		);
		
		// Проверка на наличие
		if (!$thread)
			return $this->_returnNotFound();

		// Название треда
		$this->tag->prependTitle($thread->subject ? $thread->subject : 'Thread #'.$thread->id);

		// Описание раздела, если есть
		if ($this->board->description)
			$this->tag->setDescription($this->board->description);

		// Передаём переменную содержащую борду, номер треда и тред
		$this->view->setVars([
			'board' 	=> $this->board,
			'thread_id' => $this->thread_param,
			'thread' 	=> $thread
		]);
	}
	
	public function catalogAction()
	{
		// Если нет такого раздела - разворачиваемся и уходим
		if (!$this->board)
			return $this->_returnNotFound();
	
		// Поиск тредов
		$threads = Post::find(
			[ 'type = "thread" and board = :board:', 'order' => 'isSticky DESC, bump DESC', 'bind' => [
				'board' => $this->board->slug
			]]
		);
		
		// Проверка на их наличие
		if (!$threads)
			$this->_returnNoThreads();
		
		// Название каталога
		$this->tag->prependTitle('Сatalog');
		
		// Передаём переменную содержащую раздел и тред
		$this->view->setVars([
			'board' 	=> $this->board,
			'thread_id' => $this->thread_param,
			'threads' 	=> $threads
		]);
	}
	
	public function filesAction()
	{
		// Если нет такого раздела - разворачиваемся и уходим
		if (!$this->board)
			return $this->_returnNotFound();

		// Поиск файлов
		$files = File::find(
			[ 'board = :board:', 'bind' => [
				'board' => $this->board_param
			]]
		);
		
		// Название каталога
		$this->tag->prependTitle('Files');
		
		// Передаём переменную содержащую раздел и тред
		$this->view->setVars([
			'board' 	=> $this->board,
			'thread_id' => $this->thread_param,
			'files' 	=> $files
		]);
	}

}