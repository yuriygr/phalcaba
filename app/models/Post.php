<?php
use Phalcon\Tag as Tag;

class Post extends ModelBase
{

    public $id;
    
    public $type;
    
    public $parent;
    
    public $board;
    
    public $subject;
    
    public $timestamp;
    
    public $text;
    
    public $owner;
    
    public $bump;
    
    public $sage;
    
    public $isLocked;

    public function initialize()
    {
        $this->hasMany("id", "File", "owner");
    }
	// После того как выбрали данные из базы
	public function afterFetch()
	{
	    $config =  \Phalcon\DI\FactoryDefault::getDefault()->getShared('config');
        $url =  \Phalcon\DI\FactoryDefault::getDefault()->getShared('url');
        
	    $this->name = $config->site->defalutName;
	    $this->time = $this->formatDate($this->timestamp);
	    // Ссылка на пост
	    $this->link = Tag::linkTo([
	    	$url->get([ 'for' => 'thread-link', 'board' => $this->board, 'id' => ($this->parent == 0 ? $this->id : $this->parent) ]).'#'.$this->id,
	    	'#' . $this->id,
	    	'data-reflink' => $this->id
	    ]);
	    // Ссылка на открытие треда
	    $this->open = Tag::linkTo([
	    	$url->get([ 'for' => 'thread-link', 'board' => $this->board, 'id' => $this->id ]),
	    	'[Открыть]',
	    	'data-openthread' => $this->id
	    ]);
	    // Кол-во ответов
	    if ( $this->parent == 0 )
			$this->replies = Post::find("parent = $this->id and type = 'reply' and board = '$this->board'")->count();
	}
	// Получение ответов на пост. TODO: Так же можно рекурсивные комменты делать!
	public function getReply( $limit = null )
	{
		$offset = ($this->replies - $limit) < 0 ? null : ($this->replies - $limit);
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
		return $this->getFile("board = '{$this->board}'");
	}
	
}
