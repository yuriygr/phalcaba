<?php

namespace Chan\Controllers;

use \Phalcon\Utils\Slug;
use \Phalcon\Mvc\View;

use \Chan\Models\Board;
use \Chan\Models\Post;
use \Chan\Models\File;
use \Chan\Models\Stoplist;

class ChanController extends ControllerBase
{

	public function initialize()
	{
		parent::initialize();

		$this->board_param 	= $this->dispatcher->getParam('board', 'string');
		$this->thread_param	= $this->dispatcher->getParam('id', 'int', '0');
		$this->userIp 		= $this->request->getClientAddress();
		
		$this->board = Board::findFirst(
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

		if ( $this->request->isPost() && $this->request->isAjax() ) {

			$this->view->disable();

			try {

				$manager = new \Phalcon\Mvc\Model\Transaction\Manager();
				$escaper = new \Phalcon\Escaper();

				$transaction = $manager->get();

				$board_slug = $this->board->slug;
				
				$parent = $this->request->getPost('parent', 'int', 0);
				
				$kasumi = $this->request->getPost('kasumi');
				$kasumi = $this->filter->sanitize($kasumi, 'striptags');
				$kasumi = $escaper->escapeHtml($kasumi);
				
				$name = null;

				$shampoo = $this->request->getPost('shampoo');
				$shampoo = $this->parse->make($shampoo, $board_slug, $parent);
				
				// I'm so sorry
				$userIp = $this->userIp;

				$isSage = $this->request->getPost('sage', 'int', 0);
				
				$isThread = ($parent == 0);
				$isPost = ($parent != 0);

				$hasFile = $this->request->hasFiles();

				$board = Board::findFirst(
					[ 'slug = :slug:', 'bind' => [
						'slug' => $board_slug
					]]
				);

				// Проверка наличия раздела
				if (!$board) {
					throw new \Phalcon\Exception('Такого раздела не существует');
				}

				// Проверка на закрыт ли он
				if ($board->isLocked) {
					throw new \Phalcon\Exception('Раздел закрыт, в него постить нельзя');
				}
				
				// Если пост в тред, то проверяем, есть ли тред
				if ($isPost) {
					$thread = Post::findFirst(
						[ 'id = :id: and type = "thread" and board = :board:', 'bind' => [
							'id' => $parent,
							'board' => $board_slug
						]]
					);
					// Проверка наличия треда
					if (!$thread)
						throw new \Phalcon\Exception('Такого треда не существует');

					// Проверка на закрыт ли он
					if ($thread->isLocked)
						throw new \Phalcon\Exception('Тред закрыт, в него постить нельзя');
				}

				// Проверка на наличие текста
				if (!$shampoo) {
					throw new \Phalcon\Exception('Введите сообщение');
				}

				// Проверка длины заголовка
				if ($this->_checkSubject($kasumi)) {
					throw new \Phalcon\Exception('Заголовок слишком длинный');
				}
					
				// Проходим проверку на спам
				if ($this->_checkStoplist($shampoo)) {
					throw new \Phalcon\Exception('Ой-ой, шалунишка');
				}

				// Проходим проверку скорость постинга
				if (!$this->_checkLastPost()) {
					throw new \Phalcon\Exception('Вы постите слишком быстро');
				}

				// Создаём пост
				$post = new Post();
				$post->setTransaction($transaction);
				$post->type 		= 	$isThread 	? 'thread' : 'reply';
				$post->parent 		= 	$parent;
				$post->board 		= 	$board_slug;
				$post->name			=	$name 		?? null;
				$post->subject		=	$kasumi 	?? null;
				$post->timestamp 	=	time();
				$post->text			=	$shampoo 	?? null;
				$post->userIp 		= 	$userIp;
				$post->bump 		= 	$isThread 	? time() : 0;
				$post->isSage 		= 	$isSage;

				/**
				 * Добавляем пост
				 */
				if (!$post->save()) {
					$transaction->rollback('Пост не прошёл: ' . $post->getMessages()[0]);
				}
				
				/**
				 * Attach file
				 */
				if ($hasFile) {
					$this->uploader->setRules([
						'dynamic' 	=> $this->config->application->filesDir . $board_slug . '/',
						'maxsize'   => 15240000,
						'mimes'     => $this->config->site->allowedFiles->toArray(),
						'hash_size' => 10,
					]);

					if ($this->uploader->isValid()) {

						$this->uploader->move();

						foreach ($this->uploader->getInfo() as $fileInfo) {

							// Save model
							$file = new File();
							$file->setTransaction($transaction);
							$file->slug 	= $fileInfo['slug'];
							$file->board 	= $post->board;
							$file->type 	= $fileInfo['extension'];
							$file->owner 	= $post->id;
							$file->o_width 	= $fileInfo['width'];
							$file->o_height = $fileInfo['height'];

							if (!$file->save()) {
								$transaction->rollback('Файл не прошёл: '. $file->getMessages()[0]);
								$this->uploader->truncate();
							}
						}
					} else {
						$transaction->rollback($this->uploader->getErrors()[0]);
					}
				}

				/**
				 * Bump thread if needed
				 */
				if ($isPost && !$this->_bumpThread($post)) {
					$transaction->rollback('Тред не бампнут');
				}

				/**
				 * Trimmed thread after create thread
				 */
				if ($isThread && !$this->_trimThreads()) {
					$transaction->rollback('Треды не почистило');
				}

				/**
				 * Все хорошо, работаем дальше
				 */
				$transaction->commit();

				/**
				 * Записываем время создания этого поста
				 */
				$this->session->set('lastPost', time());

				/**
				 * Refresh thread after new post
				 */
				if ($isPost) {
					return $this->_returnJson([
						'success' => 'Post sent',
						'refreshThread' => true
					]);
				}

				/**
				 * Redirect to thread after submit
				 */
				if ($isThread) {
					return $this->_returnJson([
						'success' => 'Thread created, redirect...',
						'redirect' => $this->url->get([ 'for' => 'chan.thread.link', 'board' => $post->board, 'id' => $post->id ])
					]);
				}

			} catch (\Phalcon\Mvc\Model\Transaction\Failed $e) {
				return $this->_returnJson([ 'error' => $e->getMessage() ]);
			} catch (\Phalcon\Exception $e) {
				return $this->_returnJson([ 'error' => $e->getMessage() ]);
			}
		}
		return $this->_redirectHome();
	}
	
	public function boardAction()
	{	
		// Если нет такого раздела - разворачиваемся и уходим
		if (!$this->board)
			return $this->_returnNotFound();
			
		$currentPage = $this->dispatcher->getParam('page', 'int', 1);
		
		// Поиск тредов
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



	/**
	 * Check subject ssize
	 * @param  string $kasumi Post subject
	 * @return bool
	 */
	private function _checkSubject($kasumi)
	{
		return iconv_strlen($kasumi) >= $this->config->site->subjectLimit;
	}

	/**
	 * Check message to spam
	 * @param  string $text User message
	 * @return bool
	 */
	private function _checkStoplist($text)
	{
		// Собираем все плохие слова
		$stoplist = Stoplist::find();

		// Проходимся по ним
		foreach ($stoplist as $badword) {
			if (stripos($text, $badword->word) !== false) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Проверка разницы времени последнего поста
	 * @return bool
	 */
	private function _checkLastPost()
	{
		if (!$this->session->has('lastPost')) return true;

		if ((time() - $this->session->get('lastPost')) >= $this->config->site->timeLimit)
			return true;
		else
			return false;
	}

	/**
	 * Bump thread
	 * @param  object $post User post
	 * @return bool
	 */
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

	/**
	 * Trim threads
	 * Не работает на данный момент
	 * @return bool
	 */
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