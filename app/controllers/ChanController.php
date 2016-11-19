<?php

namespace Chan\Controllers;

use \Phalcon\Utils\Slug;
use \Phalcon\Mvc\View;

use \Chan\Models\Chan;
use \Chan\Models\Post;
use \Chan\Models\Stoplist;

class ChanController extends ControllerBase
{

	public function initialize()
	{
		parent::initialize();

		$this->board_param 	= $this->dispatcher->getParam('board', 'string');
		$this->thread_param	= $this->dispatcher->getParam('id', 'int', '0');
		$this->userIp 		= $this->request->getClientAddress();
		
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

			$e = new \Phalcon\Escaper();
			
			$board_slug = $this->board->slug;
			
			$parent 	= $this->request->getPost('parent', 'int');
			
			$kasumi 	= $this->request->getPost('kasumi');
			$kasumi 	= $this->filter->sanitize($kasumi, 'striptags');
			$kasumi 	= $e->escapeHtml($kasumi);
			
			$name 		= null;

			$shampoo 	= $this->request->getPost('shampoo');
			//$shampoo	= $this->filter->sanitize($shampoo, 'striptags');
			$shampoo	= $this->parse->make($shampoo, $board_slug, $parent);
			
			// I'm so sorry
			$userIp 	= $this->userIp;

			$isSage 	= $this->request->getPost('sage') ? 1 : 0;
			
			$isThread = ($parent == 0);
			$isPost   = ($parent != 0);

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
			
			if ($isPost) {
				$thread = Post::findFirst(
					[ 'id = :id: and type = "thread" and board = :board:', 'bind' => [
						'id' => $parent,
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

			if ($this->_checkSpam($shampoo))
				return $this->_returnJson([ 'error' => 'Ой ой, шалунишка' ]);

			$post = new Post();
			$post->type 		= 	$isThread 	? 'thread' : 'reply';
			$post->parent 		= 	$parent;
			$post->board 		= 	$board_slug;
			$post->name			=	$name 		?? null;
			$post->subject		=	$kasumi 	?? null;
			$post->timestamp 	=	time();
			$post->text			=	$shampoo 	?? null;
			$post->userIp 		= 	$userIp;
			$post->bump 		= 	$isThread 	? time() : 0;
			$post->isSage 		= 	$isThread 	? 0 : $isSage;

			if ($post->save()) {

				/**
				 * Attach file
				 */
				if ($this->request->hasFiles() == true) {
					return $this->_returnJson([ 'error' => 'Молодой человек, не для вас это сделанно' ]);
					/*
					$uploader = $this->_uploadFile();

					if ($uploader->isValid() === true) {
							return $this->_returnJson([ 'error' => $uploader->getInfo()->filename]);
						if ($uploader->move()) {
							$file = new File();
							$file->slug			=	$uploader->getInfo()->filename;
							$file->board 		=	$post->board;
							$file->type			=	$uploader->getInfo()->extension;
							$file->owner		=	$post->id;
							$file->o_width 		= 	$uploader->getInfo()->size;
							$file->o_height 	= 	$uploader->getInfo()->size;

							if (!$file->save())
								return $this->_returnJson([ 'error' => 'Файл не сохранился' ]);

						} else {
							$uploader->getErrors();
						}
					} else {
						return $this->_returnJson([ 'error' => 'Файл не загрузился']);
					}*/
				}

				/**
				 * Bump thread if needed
				 */
				if ($isPost) {
					if (!$this->_bumpThread($post)) {
						return $this->_returnJson([ 'error' => 'Тред не бампнут' ]);
					}
				}


				/**
				 * Trimmed thread after create thread
				 */
				if ($isThread) {
					if (!$this->_trimThreads()) {
						return $this->_returnJson([ 'error' => 'Треды не почистило' ]);
					}
				}

				/**
				 * Redirect to thread after submit post
				 */
				if ($isPost) {
					return $this->_returnJson([
						'success' => 'Пост отправлен',
						'refreshThread' => true
					]);
				}

				if ($isThread) {
					return $this->_returnJson([
						'success' => 'Тред создан, перенаправляю',
						'redirect' => $this->url->get([ 'for' => 'chan.thread.link', 'board' => $post->board, 'id' => $post->id ])
					]);
				}
				
			// Если не добавился пост
			} else {
				return $this->_returnJson([ 'error' => 'Очень странная ошибка' ]);
			}
			
		}
		return $this->_redirectHome();
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
			'data' 	=> $threads,
			'limit'	=> $this->config->site->threadLimit,
			'page' 	=> $currentPage
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
		$this->tag->prependTitle($thread->subject ? $thread->subject : 'Thread #' . $thread->id);

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

	public function searchAction()
	{
		// Если нет такого раздела - разворачиваемся и уходим
		if (!$this->board)
			return $this->_returnNotFound();

		$hashtag = $this->request->get('hashtag', 'string', null);
		
		// Название каталога
		$this->tag->prependTitle('Search #' . $hashtag);
		
		// Передаём переменную содержащую раздел и тред
		$this->view->setVars([
			'board' 	=> $this->board,
			'thread_id' => $this->thread_param,
			'hashtag' 	=> $hashtag
		]);
	}






	private function _uploadFile()
	{
		$uploader = $this->di->get('uploader');

		// setting up uloader rules
		$uploader->setRules([
			'directory' =>  $this->config->application->filesDir,
			'minsize'   =>  1000,   // bytes
			'maxsize'   =>  1000000,// bytes
			'mimes'     =>  $this->config->site->allowedFiles,

			'sanitize' 	=> true,
			'hash'     	=> 'md5'
		]);
	
		return $uploader;
	}
	private function _checkSpam($text)
	{
		// Собираем все плохие сслова
		$stoplist = Stoplist::find();

		// Проходимся по ним
		foreach ($stoplist as $badword) {
			if (stripos($text, $badword->word) !== false) {
				// if ($badword->ban) $this->ban->add();
				return true;
			}
		}
		return false;
	}
	private function _bumpThread($post)
	{
		$thread = Post::findFirst(
			[ 'id = :id: and board = :board:', 'bind' => [
				'id' => $post->parent,
				'board' => $post->board
			]]
		);

		if ($thread->countReply() <= $this->config->site->postLimit && $post->isSage == 0) {
			$thread->bump = $post->timestamp;
			if (!$thread->update())
				return false;
		}
		return true;
	}
	private function _trimThreads()
	{
		return true;
		/*
		// Считаем сколько всего тредов можно содержать
		$limit = $this->config->site->threadLimit * $this->config->site->pageLimit;
		$count = Post::find(
			[ 'type = "thread" and board = :board:', 'order' => 'bump DESC', 'bind' => [
				'board' => $this->board_param
			]]
		)->count();
		$offset = $count - $limit;

		if ( $offset <= 0 )

		// Находим треды и удаляем
		$threads = Post::find(
			[ 'type = "thread" and board = :board:', 'limit' => $limit, 'offset' => $count - $limit, 'order' => 'bump DESC', 'bind' => [
				'board' => $this->board_param
			]]
		);
		if ($threads->delete())
			return true;
		else
			return false;*/
	}
}