<?php

namespace Chan\Models;

use \Phalcon\Utils\Timeformat;
use \Phalcon\Mvc\Model\Relation;
use \Phalcon\Tag;

class Post extends ModelBase
{

	public $id;
	
	public $type;
	
	public $parent;
	
	public $board;
	
	public $name;
	
	public $subject;
	
	public $timestamp;

	public $text;

	public $userIp;
	
	public $bump;
	
	public $isSage;

	public $isLocked;
	
	public $isSticky;

	public function initialize()
	{
		// Имеет много картинок
		$this->hasMany("id", "Chan\Models\File", "owner", [
			"alias" => "file",
			"foreignKey" => [
				"action" => Relation::ACTION_CASCADE,
			]
		]);
	}
	// Перед удалением удалим все посты в треде
	public function beforeDelete()
	{
		if ($this->parent == 0)
			Post::find("parent = $this->id and type = 'reply' and board = '$this->board'")->delete();

		return true;
	}
	// После того как выбрали данные из базы
	public function afterFetch()
	{	
		$url = $this->di->getDefault()->getUrl();
		// Ссылка на скролл к посту
		$this->anchor = Tag::linkTo([
			$url->get([ 'for' => 'chan.thread.link', 'board' => $this->board, 'id' => ($this->parent == 0 ? $this->id : $this->parent) ]).'#'.$this->id,
			'#'
		]);
		// Ссылка на пост
		$this->link = Tag::linkTo([
			$url->get([ 'for' => 'chan.thread.link', 'board' => $this->board, 'id' => ($this->parent == 0 ? $this->id : $this->parent) ]).'#'.$this->id,
			$this->id,
			'data-reply' => $this->id,
			'data-reply-thread' => ($this->parent == 0 ? $this->id : $this->parent)
		]);
		// Ссылка на открытие треда
		$this->open = Tag::linkTo([
			$url->get([ 'for' => 'chan.thread.link', 'board' => $this->board, 'id' => $this->id ]),
			'[Open]',
			'data-thread-open' => $this->id
		]);
	}
	// Выдаём имя
	public function getName()
	{
		$config =  $this->di->getDefault()->getConfig();
		return !empty($this->name) ? $this->name : $config->site->defalutName;
	}
	// Выдаём дату
	public function getTime()
	{
		return Timeformat::normal($this->timestamp);
	}
	// Кол-во ответов
	public function getNuberLink()
	{
		return $this->anchor.$this->link;
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
	// Получаем файлы из того же раздела что и пост
	public function getFiles()
	{
		$files = $this->getFile("board = '{$this->board}'");
		
		if ($files->count() >= 1)
			return $files;
		else
			return false;
		
	}
}
