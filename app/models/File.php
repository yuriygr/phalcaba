<?php

namespace Chan\Models;

class File extends ModelBase
{

	public $id;
	
	public $slug;
	
	public $board;
	
	public $type;
	
	public $owner;
	
	public $o_width;
	
	public $o_height;
	
	public function initialize()
	{
		$this->belongsTo("owner", "Chan\Models\Post", "id");
	}
	// Удаляя модель удалим и файлы
	public function beforeDelete()
	{
		unlink( $this->getLink('origin') );
		unlink( $this->getLink('thumb')  );
	}
	// Получаем ссылку на файл
	public function getLink( $type = 'origin')
	{
		if ($type == 'origin')
			return '/file/' . $this->board . '/' . $this->slug . '.' . $this->type;
			
		if ($type == 'thumb')
			return '/file/' . $this->board . '/' . $this->slug . '_t.' . 'jpg';
	}
	// Получаем разрешение файла
	public function getResolution()
	{
		return $this->o_width . 'x' . $this->o_height;
	}
	
}
