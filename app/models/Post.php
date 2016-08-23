<?php

use \Phalcon\Utils\Timeformat as Timeformat;

class Post extends ModelBase
{

	public $id;
	
	public $type;
	
	public $parent;
	
	public $board;
	
	public $subject;
	
	public $timestamp;

	public $name;
	
	public $text;
	
	public $owner;
	
	public $bump;
	
	public $sage;
	
	public $isLocked;
	
	public $isSticky;

	public function initialize()
	{
		$this->hasMany("id", "File", "owner");
	}
	// После того как выбрали данные из базы
	public function afterFetch()
	{
		$config 	=  $this->di->getDefault()->getConfig();
		$url 		=  $this->di->getDefault()->getUrl();

		$this->name = isset($this->name) ? $this->name : $config->site->defalutName;
		$this->time = Timeformat::normal($this->timestamp);
		// Ссылка на пост
		$this->link = Phalcon\Tag::linkTo([
			$url->get([ 'for' => 'chan-thread-link', 'board' => $this->board, 'id' => ($this->parent == 0 ? $this->id : $this->parent) ]).'#'.$this->id,
			'#' . $this->id,
			'data-reply' => $this->id,
			'data-reply-thread' => ($this->parent == 0 ? $this->id : $this->parent)
		]);
		// Ссылка на открытие треда
		$this->open = Phalcon\Tag::linkTo([
			$url->get([ 'for' => 'chan-thread-link', 'board' => $this->board, 'id' => $this->id ]),
			'[Открыть]',
			'data-thread-open' => $this->id
		]);
	}
	// Кол-во ответов
	public function countReply()
	{
		if ($this->parent == 0)
			return Post::find("parent = $this->id and type = 'reply' and board = '$this->board'")->count();
	}
	// Получение ответов на пост. WOW: Так же можно рекурсивные комменты делать!
	public function getReply( $limit = null )
	{
		$offset = ($this->countReply() - $limit) < 0 ? null : ($this->countReply() - $limit);
		$reply = Post::find(
			[ 'parent = :id: and type = "reply" and board = :board:', 'group' => 'id', 'limit' => $limit, 'offset' => $offset, 'bind' => [
				'id' => $this->id,
				'board' => $this->board
			]]
		);
		return $reply;
	}	
	// Получаем файл из того же раздела
	public function getFiles()
	{
		$files = $this->getFile("board = '{$this->board}'");
		
		if ($files->count() >= 1)
			return $files;
		else
			return false;
		
	}
	
}
