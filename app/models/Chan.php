<?php

namespace Chan\Models;

use \Phalcon\Mvc\Model\Relation;

class Chan extends ModelBase
{

	public $id;

	public $slug;
	
	public $name;
	
	public $description;

	public $category;	

	public $isHide;
	
	public $isLocked;
	
	public $isLeed;

	public function initialize()
	{
		$this->hasMany("slug", "Chan\Models\File", "board", [
			"alias" => "file",
			"foreignKey" => [
				"action" => Relation::ACTION_CASCADE,
			]
		]);
	}

	// После того как выбрали данные из базы
	public function afterFetch()
	{

	}
}
