<?php

class News extends ModelBase
{
	
    public $id;

    public $slug;

    public $title;

    public $description;
    
	public $content;

	public $created_at;

	public $modified_in;


	public function initialize()
	{
	}
	// После того как выбрали данные из базы
	public function afterFetch()
	{
		// Дата атомного формата
		$this->created_format = date(DATE_ATOM, $this->created_at);

		// Дата в приятном формате
		if ($this->created_at)
			$this->created_at = $this->formatDate($this->created_at);

		if ($this->modified_in)
			$this->modified_in = $this->formatDate($this->modified_in);
	}
	
	public function getContent()
	{
		return $this->content;
	}
	public function getDate()
	{
		return '<time datetime="' . $this->created_format . '">' . $this->created_at . '</time>';
	}
}
