<?php

use \Phalcon\Utils\Slug as Slug;

class ChanController extends ControllerBase
{

    public function initialize()
    {
		$this->board_param 	= $this->dispatcher->getParam('board');
		$this->thread_param	= $this->dispatcher->getParam('id', 'int', '0');
		
		$this->board 		= Chan::findFirst(
			[ 'slug = :slug:', 'bind' => [
				'slug' => $this->board_param
			]]
		);
    }
    
 	public function addAction()
	{

		if ( $this->request->isPost() && $this->request->isAjax() ) {
			
			$board_slug = $this->request->getPost('board_slug');
			$thread_id 	= $this->request->getPost('thread_id');
			
			$kasumi 	= $this->request->getPost('kasumi');
			$shampoo 	= $this->request->getPost('shampoo');
			$sage 		= $this->request->getPost('sage') ? 1 : 0;
			$files 		= $this->request->getPost('files');

			// Проверка наличия раздела
			$board = Chan::findFirst(
				[ 'slug = :slug:', 'bind' => [
					'slug' => $board_slug
				]]
			);
			if (!$board)
				return $this->_returnJson([ 'error' => 'Такого раздела не существует' ]);
			
			if ($thread_id != 0) {
				$thread = Post::findFirst(
					[ 'id = :id: and type = "thread" and board = :board:', 'bind' => [
						'id' => $thread_id,
						'board' => $board_slug
					]]
				);
				// Проверка наличия треда
				if (!$thread)
					return $this->_returnJson([ 'error' => 'Такого треда не существует' ]);
				// Проверка на закрыт ли он
				if ($thread->isLocked)
					return $this->_returnJson([ 'error' => 'Тред закрыт. В него постить нельзя' ]);
			}
			
			// Проверка на наличие текста
			if (!$shampoo)
				return $this->_returnJson([ 'error' => 'Введите сообщение' ]);

    		/*   
    		
    		    ТУТ ТУПО ОБРАБАТЫВАЕМ ФАЙЛ!
    		
    		*/

			$post = new Post();
			$parse = new \Phalcon\Utils\Parse;
			// Обрабатываем Тему
			$post->subject		=	$this->filter->sanitize($kasumi, 'striptags');
			// Задаём время
			$post->timestamp 	=	time();
			// Обрабатываем Сообщение
			$post->text			=	$this->filter->sanitize($shampoo, 'striptags');
			$post->text			=	$parse->make($post->text);

			$post->type 		= 	($thread_id == 0) ? 'thread' : 'reply';
			$post->parent 		= 	$thread_id;
			$post->board 		= 	$board_slug;
			$post->owner 		= 	'admin';
			$post->bump 		= 	($thread_id == 0) ? time() : 0;
			$post->sage 		= 	($thread_id == 0) ? 0 : $sage;

			if ($post->save()) {
				// Добавление картинки в базу
				if ($files) {
					$file = new File();
		    		$file->slug = "DFrgf";
		    		$file->board = $post->board;
		    		$file->type = "jpg";
		    		$file->owner = $post->id;

					if (!$file->save())
						return $this->_returnJson([ 'error' => 'Файл не загружен. Но пост прошёл.' ]);
				}
				
				// Добавление бампа
				if ($post->parent != 0) {
					$thread = Post::findFirstById($post->parent);
					if ($thread->replies < $this->config->site->postLimit and $post->sage == 0)
						$thread->bump = $post->timestamp;

					if (!$thread->update())
						return $this->_returnJson([ 'error' => 'Тред не бампнут. Но пост прошёл.' ]);
				}
				
				// Редиректим под конец куда нибудь
				if ($post->parent != 0) {
					// Если пост ответ	
					return $this->_returnJson([
						'redirect' => $this->url->get([ 'for' => 'thread-link', 'board' => $thread->board, 'id' => $post->parent ])
					]);
				} else {
					//Если пост тред
					return $this->_returnJson([
						'redirect' => $this->url->get([ 'for' => 'thread-link', 'board' => $post->board, 'id' => $post->id ])
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
			[ 'type = "thread" and board = :board:', 'order' => 'bump DESC', 'bind' => [
				'board' => $this->board->slug
			]]
		);
		$paginator = new \Phalcon\Paginator\Adapter\Model([
			'data' => $threads,
			'limit'=> $this->config->site->threadLimit,
			'page' => $currentPage
		]);
		$threads = $paginator->getPaginate();
		
		// Проверка на их наличие
		if (!$threads->items)
			$this->_returnNoThreads();


		// Название раздела
		$this->tag->setTitle($this->board->name);
		
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
		$this->tag->setTitle($thread->subject ? $thread->subject : 'Тред #'.$thread->id);
		
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
			[ 'type = "thread" and board = :board:', 'order' => 'bump DESC', 'bind' => [
				'board' => $this->board->slug
			]]
		);
		
		// Проверка на их наличие
		if (!$threads)
			$this->_returnNoThreads();
		
		// Название каталога
		$this->tag->setTitle('Каталог - '.$this->board->name);
		
		// Передаём переменную содержащую раздел и тред
        $this->view->setVars([
            'board' 	=> $this->board,
            'thread_id' => $this->thread_param,
            'threads' 	=> $threads
        ]);
	}

}