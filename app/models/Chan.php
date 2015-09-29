<?php

class Chan extends ModelBase
{

	public $id;

	public $slug;
	
	public $name;
	
	public $description;
	
	public $hide;

	public function initialize()
	{
	}

	// После того как выбрали данные из базы
	public function afterFetch()
	{

	}
}
