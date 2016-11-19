<?php

namespace Chan\Models;

use \Phalcon\Utils\Timeformat;

class Page extends ModelBase
{

	public $id;

	public $slug;

	public $name;

	public $type;

	public $text;

	public $created_at;

	public $modified_in;

	public $isComments;

	public $isHide;

	// Meta-tag
	public $meta_description;

	public $meta_keywords;



	public function initialize()
	{
		// Не записываем при редактировании сюда
		$this->skipAttributesOnUpdate(array('created_at'));

		// Не записываем при создании сюда
		$this->skipAttributesOnCreate(array('modified_in'));
	}

	// После того как выбрали данные из базы
	public function afterFetch()
	{	
		// Дата атомного формата
		$this->created_format = Timeformat::atom($this->created_at);

		// Дата в приятном формате
		if ($this->created_at)
			$this->created_at = Timeformat::generate($this->created_at);

		if ($this->modified_in)
			$this->modified_in = Timeformat::generate($this->modified_in);
	}

	public function getName()
	{
		return $this->name;
	}

	public function getContent()
	{
		return $this->text;
	}

	public function getDate()
	{
		return '<time datetime="' . $this->created_format . '">' . $this->created_at . '</time>';
	}
}
