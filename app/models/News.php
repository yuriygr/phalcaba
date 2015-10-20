<?php
use Phalcon\Tag as Tag;

class News extends ModelBase
{

    public $id;
    
    public $timestamp;
    
    public $text;


	public function initialize()
	{
	}
	// После того как выбрали данные из базы
	public function afterFetch()
	{
	    $this->time = $this->formatDate($this->timestamp);
	}
}
