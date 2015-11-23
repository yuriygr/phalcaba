<?php

namespace Phalcon;

class NTag extends \Phalcon\Tag
{
	static $description = null;
	static $keywords = null;
	
	public function setDescription($param)
	{
		self::$description = $param;
	}
	public static function getDescription()
	{
		if (self::$description != null)
			return '<meta name="description" content="'.self::$description.'">'."\r\n";
	}
	public function setKeywords($param)
	{
		self::$keywords = $param;
	}
	public static function getKeywords()
	{
		if (self::$keywords != null)
			return '<meta name="keywords" content="'.self::$keywords.'">'."\r\n";
	}

	public function getOmitted($count) {
		$cases 	= [2, 0, 1, 1, 1, 2];
		$titles = ['сообщение', 'сообщения', 'сообщений'];
		return $count.' '.$titles[ ($count%100 > 4 && $count %100 < 20) ? 2 : $cases[min($count%10, 5)] ].' пропущено.';
	}
}