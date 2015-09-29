<?php

namespace Phalcon;

class NTag extends \Phalcon\Tag
{
	public function getOmitted($count) {
		$cases 	= [2, 0, 1, 1, 1, 2];
		$titles = ['сообщение', 'сообщения', 'сообщений'];
		return $count.' '.$titles[ ($count%100 > 4 && $count %100 < 20) ? 2 : $cases[min($count%10, 5)] ].' пропущенно.';
	}
}